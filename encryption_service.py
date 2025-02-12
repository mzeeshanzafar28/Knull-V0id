from flask import Flask, request, jsonify
from Crypto.Cipher import AES
import base64
import os

app = Flask(__name__)

SECRET_KEY = os.urandom(16)  # 16-byte AES key

def encrypt_message(message):
    cipher = AES.new(SECRET_KEY, AES.MODE_EAX)
    nonce = cipher.nonce
    ciphertext, tag = cipher.encrypt_and_digest(message.encode('utf-8'))
    # Return the concatenation of nonce + ciphertext, plus the nonce separately
    return base64.b64encode(nonce + ciphertext).decode('utf-8'), base64.b64encode(nonce).decode('utf-8')

def decrypt_message(encrypted_message, iv):
    try:
        nonce = base64.b64decode(iv)
        encrypted_data = base64.b64decode(encrypted_message)
        # Remove the nonce (first len(nonce) bytes) from the encrypted data
        ciphertext = encrypted_data[len(nonce):]
        cipher = AES.new(SECRET_KEY, AES.MODE_EAX, nonce=nonce)
        decrypted_message = cipher.decrypt(ciphertext).decode('utf-8')
        return decrypted_message
    except Exception as e:
        #return f"Decryption failed: {str(e)}"
        return f"Dust Cleared by Void"


@app.route('/encrypt', methods=['POST'])
def encrypt():
    data = request.json
    message = data.get("message")
    if not message:
        return jsonify({"error": "No message provided"}), 400
    encrypted_message, iv = encrypt_message(message)
    return jsonify({"encrypted_message": encrypted_message, "iv": iv})

@app.route('/decrypt', methods=['POST'])
def decrypt():
    data = request.json
    encrypted_message = data.get("encrypted_message")
    iv = data.get("iv")
    if not encrypted_message or not iv:
        return jsonify({"error": "Missing parameters"}), 400
    decrypted = decrypt_message(encrypted_message, iv)
    return jsonify({"decrypted_message": decrypted})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
