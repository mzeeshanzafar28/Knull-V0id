from flask import Flask, request, jsonify
from Crypto.Cipher import AES
import base64
import oqs

app = Flask(__name__)

kem = oqs.KeyEncapsulation("Kyber512")
public_key = kem.generate_keypair()
private_key = kem

def encrypt_message(message):
    """
    Encrypts a message using Kyber KEM for key exchange and AES for encryption.
    """
    kyber_ciphertext, shared_secret = kem.encap_secret(public_key)

    cipher = AES.new(shared_secret[:16], AES.MODE_EAX)
    nonce = cipher.nonce
    ciphertext_aes, tag = cipher.encrypt_and_digest(message.encode('utf-8'))

    return (
        base64.b64encode(kyber_ciphertext).decode('utf-8'),  # Kyber ciphertext
        base64.b64encode(nonce + ciphertext_aes).decode('utf-8'),  # Encrypted message
        base64.b64encode(nonce).decode('utf-8')  # IV (nonce)
    )

def decrypt_message(kyber_ciphertext, encrypted_message, iv):
    """
    Decrypts a message using Kyber KEM for key exchange and AES for decryption.
    """
    try:
        kyber_ciphertext = base64.b64decode(kyber_ciphertext)
        shared_secret = private_key.decap_secret(kyber_ciphertext)

        iv = base64.b64decode(iv)
        encrypted_data = base64.b64decode(encrypted_message)
        ciphertext_aes = encrypted_data[len(iv):]  # Remaining bytes are AES ciphertext

        cipher = AES.new(shared_secret[:16], AES.MODE_EAX, nonce=iv)
        decrypted_message = cipher.decrypt(ciphertext_aes).decode('utf-8')
        return decrypted_message

    except Exception as e:
        return "Dust Cleared by Void"

@app.route('/encrypt', methods=['POST'])
def encrypt():
    data = request.json
    message = data.get("message")
    if not message:
        return jsonify({"error": "No message provided"}), 400

    kyber_ciphertext, encrypted_message, iv = encrypt_message(message)

    return jsonify({
        "kyber_ciphertext": kyber_ciphertext,
        "encrypted_message": encrypted_message,
        "iv": iv
    })

@app.route('/decrypt', methods=['POST'])
def decrypt():
    data = request.json
    kyber_ciphertext = data.get("kyber_ciphertext")
    encrypted_message = data.get("encrypted_message")
    iv = data.get("iv")

    if not kyber_ciphertext or not encrypted_message or not iv:
        return jsonify({"error": "Missing parameters"}), 400

    decrypted = decrypt_message(kyber_ciphertext, encrypted_message, iv)

    return jsonify({"decrypted_message": decrypted})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
