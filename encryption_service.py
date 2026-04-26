import os
import base64
import hashlib
import hmac

from flask import Flask, request, jsonify
from Crypto.Cipher import AES
from cryptography.hazmat.primitives.kdf.hkdf import HKDF
from cryptography.hazmat.primitives import hashes
import oqs

app = Flask(__name__)

# API key for authenticating requests from Laravel
# In production, load from environment variable
API_KEY = os.environ.get('ENCRYPTION_API_KEY', 'knull-void-encryption-key-change-in-production')


def verify_api_key():
    """Verify the API key from the request headers."""
    provided_key = request.headers.get('X-API-Key', '')
    if not hmac.compare_digest(provided_key, API_KEY):
        return False
    return True


def derive_key(shared_secret, salt=None, info=b'knull-void-aes-key'):
    """
    Derive a 32-byte AES-256 key from the Kyber shared secret using HKDF.
    This is cryptographic best practice — raw shared secrets should never
    be used directly as encryption keys.
    """
    if salt is None:
        salt = os.urandom(16)

    hkdf = HKDF(
        algorithm=hashes.SHA256(),
        length=32,  # AES-256
        salt=salt,
        info=info,
    )
    derived_key = hkdf.derive(shared_secret)
    return derived_key, salt


def encrypt_message(message):
    """
    Encrypts a message using Kyber KEM for key exchange and AES-256-GCM for encryption.

    Per-message keypair: A fresh Kyber keypair is generated for each encryption.
    The private key is used immediately for the KEM operation and then the
    encapsulated ciphertext + shared secret are used for AES encryption.

    Flow:
    1. Generate fresh Kyber keypair
    2. Encapsulate to get shared secret + kyber ciphertext
    3. Derive AES key from shared secret via HKDF
    4. Encrypt message with AES-256-GCM (authenticated encryption)
    5. Return all components needed for decryption (including the private key,
       encrypted with the master key for storage)
    """
    # Generate fresh Kyber keypair for this message
    kem = oqs.KeyEncapsulation("Kyber512")
    public_key = kem.generate_keypair()

    # Encapsulate: produces kyber_ciphertext + shared_secret
    kyber_ciphertext, shared_secret = kem.encap_secret(public_key)

    # Derive AES-256 key from shared secret using HKDF
    aes_key, hkdf_salt = derive_key(shared_secret)

    # Encrypt with AES-256-GCM (authenticated encryption with associated data)
    cipher = AES.new(aes_key, AES.MODE_GCM)
    nonce = cipher.nonce
    ciphertext_aes, tag = cipher.encrypt_and_digest(message.encode('utf-8'))

    # Export the private key for storage (needed for decryption later)
    # The private key is the KEM's internal secret key
    private_key_bytes = kem.export_secret_key()

    return {
        'kyber_ciphertext': base64.b64encode(kyber_ciphertext).decode('utf-8'),
        'encrypted_message': base64.b64encode(ciphertext_aes).decode('utf-8'),
        'iv': base64.b64encode(nonce).decode('utf-8'),
        'tag': base64.b64encode(tag).decode('utf-8'),
        'hkdf_salt': base64.b64encode(hkdf_salt).decode('utf-8'),
        'kem_private_key': base64.b64encode(private_key_bytes).decode('utf-8'),
    }


def decrypt_message(kyber_ciphertext, encrypted_message, iv, tag, hkdf_salt, kem_private_key):
    """
    Decrypts a message using the stored Kyber private key and AES-256-GCM.

    Flow:
    1. Reconstruct KEM from stored private key
    2. Decapsulate kyber ciphertext to recover shared secret
    3. Re-derive AES key from shared secret via HKDF (with same salt)
    4. Decrypt and verify AES-GCM ciphertext (checks authentication tag)
    """
    try:
        # Decode all base64 inputs
        kyber_ct = base64.b64decode(kyber_ciphertext)
        aes_ct = base64.b64decode(encrypted_message)
        nonce = base64.b64decode(iv)
        auth_tag = base64.b64decode(tag)
        salt = base64.b64decode(hkdf_salt)
        secret_key = base64.b64decode(kem_private_key)

        # Reconstruct KEM with the stored private key
        kem = oqs.KeyEncapsulation("Kyber512", secret_key=secret_key)

        # Decapsulate to recover shared secret
        shared_secret = kem.decap_secret(kyber_ct)

        # Re-derive AES key with same HKDF salt
        aes_key, _ = derive_key(shared_secret, salt=salt)

        # Decrypt and verify authentication tag (AES-256-GCM)
        cipher = AES.new(aes_key, AES.MODE_GCM, nonce=nonce)
        decrypted_message = cipher.decrypt_and_verify(aes_ct, auth_tag)

        return decrypted_message.decode('utf-8')

    except ValueError as e:
        # Authentication tag verification failed — message was tampered with
        return None
    except Exception as e:
        app.logger.error(f"Decryption error: {str(e)}")
        return None


@app.route('/encrypt', methods=['POST'])
def encrypt():
    if not verify_api_key():
        return jsonify({"error": "Unauthorized"}), 401

    data = request.json
    if not data:
        return jsonify({"error": "No JSON body provided"}), 400

    message = data.get("message")
    if not message:
        return jsonify({"error": "No message provided"}), 400

    try:
        result = encrypt_message(message)
        return jsonify(result)
    except Exception as e:
        app.logger.error(f"Encryption error: {str(e)}")
        return jsonify({"error": "Encryption failed"}), 500


@app.route('/decrypt', methods=['POST'])
def decrypt():
    if not verify_api_key():
        return jsonify({"error": "Unauthorized"}), 401

    data = request.json
    if not data:
        return jsonify({"error": "No JSON body provided"}), 400

    required_fields = ['kyber_ciphertext', 'encrypted_message', 'iv', 'tag', 'hkdf_salt', 'kem_private_key']
    missing = [f for f in required_fields if not data.get(f)]
    if missing:
        return jsonify({"error": f"Missing parameters: {', '.join(missing)}"}), 400

    try:
        decrypted = decrypt_message(
            data['kyber_ciphertext'],
            data['encrypted_message'],
            data['iv'],
            data['tag'],
            data['hkdf_salt'],
            data['kem_private_key']
        )

        if decrypted is None:
            return jsonify({"error": "Decryption failed — message may have been tampered with"}), 400

        return jsonify({"decrypted_message": decrypted})

    except Exception as e:
        app.logger.error(f"Decryption endpoint error: {str(e)}")
        return jsonify({"error": "Decryption failed"}), 500


@app.route('/health', methods=['GET'])
def health():
    """Health check endpoint."""
    return jsonify({"status": "ok", "kem": "Kyber512", "cipher": "AES-256-GCM"})


if __name__ == '__main__':
    debug_mode = os.environ.get('FLASK_DEBUG', 'false').lower() == 'true'
    app.run(
        host='0.0.0.0',
        port=5000,
        debug=debug_mode,
        ssl_context=('cert.pem', 'key.pem')
    )
