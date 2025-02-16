
---

# 😈 Knull-V0id

### *The Hell for Quantum Computers*

---

## 🛡️ Soldiers of the Void

*What fuels the darkness? Meet the architects of this nightmare:*

- **👑 Lord Laravel**  — The backbone of the abyss.
- **🥷 Master Vue**  — The shadow that shapes the void.
- **🥸 Uncle Tailwind**  — Styling the horror with finesse.
- **😎 Donnie Reverb**  — Echoing whispers across the realm.
- **👯 Sister Python**  — Powering the dark rituals.

---

## 🔥 Features from the Abyss

- 🕳 **Ephemeral ChatRooms** — Where secrets come to die.
- 🔗 **Secure File Transfers** — No trace left behind.
- 🛡 **Post-Quantum Encryption** — Quantum computers shudder in fear.
- 💥 **Self-Destructing Messages** — Say it, forget it, watch it burn.
- 📁 **Self-Destructing Files** — Files that fade into the void.
- ⚡ **WebSockets Implementation** — Real-time whispers from the dark.
- 🔢 **Lattice-Based Cryptography** — The cryptographic fortress.
- 🌀 **Python Microservices** — The unseen forces at work.
- 🔑 **Kyber KEM** — Post-quantum cryptographic shields engaged.

---

## 🔐 How the Encryption Mechanism Works

Void's encryption mechanism combines the power of **Kyber KEM** for post‑quantum key encapsulation with **AES** for symmetric encryption.

### Kyber KEM Basics

- **Key Generation:**  
  Kyber KEM generates a keypair—a **public key** and a **private key**.  
  - The **public key** is used by anyone who wants to send you a message.  
  - The **private key** (kept secret) is used to retrieve a shared secret during decryption.

- **Encapsulation:**  
  When encrypting a message, the sender uses your public key to perform an encapsulation operation:
  - **Kyber Ciphertext:** The encapsulation process produces a Kyber ciphertext. This value is sent along with the encrypted message.
  - **Shared Secret:** At the same time, a random shared secret is generated. This secret is then used as the key for symmetric encryption.

### Encryption Process

1. **Encapsulation with Kyber:**  
   Using the public key, the sender encapsulates a shared secret. This produces two outputs:
   - The **Kyber ciphertext** (needed for decryption).
   - The **shared secret**.

2. **AES Encryption:**  
   - The shared secret (or the first 16 bytes of it) is used as the key for AES encryption.
   - AES in EAX mode generates a unique **nonce** (or IV) for each encryption.
   - The plaintext message is encrypted with AES, producing the **AES ciphertext**.
   - The encrypted output typically includes the nonce (to be used later for decryption).

3. **Return Values:**  
   The encryption function returns:
   - The **Kyber ciphertext** (Base64-encoded) so that the recipient can decapsulate and retrieve the shared secret.
   - The **encrypted message** (Base64-encoded concatenation of the nonce and AES ciphertext).
   - The **initialization vector (IV)** (Base64-encoded nonce).

### Decryption Process

1. **Decapsulation with Kyber:**  
   The recipient uses their private key to decapsulate the received Kyber ciphertext, which retrieves the same shared secret used during encryption.

2. **AES Decryption:**  
   - The nonce (IV) is extracted from the encrypted message.
   - Using the shared secret (first 16 bytes) and the nonce, an AES cipher is recreated.
   - The AES ciphertext is decrypted back into the original plaintext.

### For Your Info

- **Public/Private Key:**  
  The public key is shared and used for encryption (encapsulation), while the private key is kept secret and used for decryption (decapsulation).

- **Kyber Ciphertext:**  
  The output from the encapsulation process; it carries the information needed for decapsulation to recover the shared secret.

- **Shared Secret:**  
  A symmetric key derived during encapsulation that is used to encrypt and decrypt the actual message with AES.

- **Nonce / IV:**  
  A unique value generated during AES encryption that ensures the ciphertext is different even if the same message is encrypted multiple times.

Together, this layered approach ensures that even if a quantum computer can break classical encryption methods, Kyber KEM remains secure, and your messages stay safe in the abyss.

---

## 🤝 Contributing to the Chaos

This hell is solo-built by *Me* 🔥 but pull requests are welcome. If you're making major changes, open an issue first to discuss them.

**Remember:** If you're changing the abyss, update the tests accordingly!

---

## 📜 License

*📝 MIT License*

---

## 🚀 How to Run

### 1️⃣ **Initial Setup**

1. **Clone the Repository**
2. Run `composer install`  *(Ensure Composer is installed)*
3. Run `npm install`  *(Ensure Node.js & npm are installed)*
4. Configure your `.env` file and set up MySQL (⚠️ Avoid Apache port 8080)
5. Run `php artisan migrate`
6. Start the Laravel server:  
   ```sh
   php artisan serve --port=8001
   ```
7. Start Vite for the frontend:  
   ```sh
   npm run dev
   ```
8. Start WebSockets:  
   ```sh
   php artisan reverb:start --port=8080
   ```
9. Start the Queue Worker:  
   ```sh
   php artisan queue:work
   ```
10. Start the Scheduler:  
    ```sh
    php artisan schedule:work
    ```

---

### 2️⃣ **Setting Up the PQC-Resistant Encryption Server**

11. Install **Python** 🐍  
12. Set up a virtual environment:
    ```sh
    python -m venv venv
    source venv/bin/activate  # Linux/Mac
    venv\Scripts\activate       # Windows
    ```
13. Install dependencies:
    ```sh
    pip install -r requirements.txt
    ```
14. Install **CMake**:
    ```sh
    sudo apt-get install cmake  # Linux/Mac
    choco install cmake         # Windows (requires Chocolatey)
    ```
15. Clone **liboqs**:
    ```sh
    git clone --depth=1 https://github.com/open-quantum-safe/liboqs
    ```
16. Build **liboqs**:
    ```sh
    cmake -S liboqs -B liboqs/build -DBUILD_SHARED_LIBS=ON
    cmake --build liboqs/build --parallel 8
    sudo cmake --build liboqs/build --target install  # Linux/Mac
    ```
17. Set your library path:
    ```sh
    export LD_LIBRARY_PATH=$LD_LIBRARY_PATH:/usr/local/lib  # Linux
    set PATH=%PATH%;C:\Program Files (x86)\liboqs\bin             # Windows
    ```
18. Clone **liboqs-python**:
    ```sh
    git clone --depth=1 https://github.com/open-quantum-safe/liboqs-python
    ```
19. Install **liboqs-python**:
    ```sh
    cd liboqs-python
    python3 -m ensurepip --upgrade   # On Windows, use python instead of python3
    pip install .
    cd ..
    ```
20. Verify the installation:
    ```sh
    python3 liboqs-python/examples/kem.py  # (Replace python3 with python on Windows)
    ```
21. **Run the encryption server:**
    ```sh
    python encryption_service.py
    ```

🚨 **Note:** If you run into installation issues, please refer to the official [liboqs-python GitHub repository](https://github.com/open-quantum-safe/liboqs-python).

---

## 📬 Email Verification

Our system uses a horror-themed email template to send verification emails. When a user registers, they receive an email that looks like a message from the abyss. The email contains a dark red background, our logo (converted to SVG or embedded as a Base64 image), and a custom “Verify Email Address” button.  
*For details on how the email template is built, please see our documentation in the `emails/` folder.*

---

## 🛡️ Reach the Hell Master

- **LinkedIn:** [https://www.linkedin.com/in/m-zeeshan-zafar-9205a1248/](https://www.linkedin.com/in/m-zeeshanzafar28/)
- **Instagram:** [https://www.instagram.com/mzeeshanzafar28/](https://www.instagram.com/mzeeshanzafar28/)
- **Portfolio1:** [https://mzeeshanzafar28.github.io/zee_folio/](https://mzeeshanzafar28.github.io/zee_folio/)
- **Portfolio2:** [https://mzeeshanzafar28.github.io/zeefolio/](https://mzeeshanzafar28.github.io/zeefolio/)
- **TryHackMe:** [https://tryhackme.com/p/mzeeshanzafar28](https://tryhackme.com/p/mzeeshanzafar28)
- **HackerRank:** [https://www.hackerrank.com/profile/mzeeshanzafar28](https://www.hackerrank.com/profile/mzeeshanzafar28)
- **W3Schools:** [https://www.w3profile.com/mzeeshanzafar28](https://www.w3profile.com/mzeeshanzafar28)

---

🎉 **You're all set! Welcome to the abyss...**  
Access the void at: [http://localhost:8001](http://localhost:8001)

---
