<img alt="void" src="void.gif" data-hpc="true" class="Box-sc-g0xbh4-0 kzRgrI" style="width:100vw;">

---

# 😈 Knull-V0id: The Hell for Quantum Computers

### *World's First Fully Open Source Onion Based Post Quantum Resistant Platform*

---

## 🛡️ Soldiers of the Void

*What fuels the darkness? Meet the architects of this nightmare:*

- **👑 Lord Laravel**  — The backbone of the abyss.
- **🥷 Master Vue**  — The shadow that shapes the void.
- **🥸 Uncle Tailwind**  — Styling the horror with finesse.
- **😎 Donnie Reverb**  — Echoing whispers across the realm.
- **👯 Sista Python**  — Powering the dark rituals.

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
- 📥 **Email Verification** — The void knows you, then you know the void.
- 🔑 **Kyber KEM** — Post-quantum cryptographic shields engaged.

---

## 🔐 How the Encryption Mechanism Works

Void's encryption mechanism uses a hybrid approach: **Kyber KEM** for post‑quantum key encapsulation and **AES-256-GCM** for authenticated symmetric encryption.

### Kyber KEM Basics

- **Key Generation:**  
  Kyber KEM generates a post-quantum keypair (public and private).  
  - The **public key** is shared to allow others to encapsulate secrets.
  - The **private key** is kept secret to retrieve those secrets.

- **Encapsulation:**  
  The sender uses the public key to perform an encapsulation:
  - **Kyber Ciphertext:** The output sent to the recipient.
  - **Shared Secret:** A high-entropy value used to derive the actual encryption keys.

### Encryption Process (Upgraded AES-256-GCM)

1. **Encapsulation with Kyber:**  
   The sender encapsulates a shared secret using the recipient's public key.
   
2. **Key Derivation (HKDF):**  
   We use **HKDF** (HMAC-based Key Derivation Function) with a random salt to derive a 256-bit AES key and a 96-bit IV from the Kyber shared secret. This ensures even if the shared secret has structural biases, the derived key is cryptographically strong.

3. **AES-256-GCM Encryption:**  
   - The message is encrypted using **AES-256 in GCM mode**.
   - GCM (Galois/Counter Mode) provides **Authenticated Encryption**, producing a 16-byte **Auth Tag**.
   - The tag ensures that any tampering with the ciphertext during transit will be detected and cause decryption to fail.

4. **Storage:**  
   The system stores:
   - **Kyber Ciphertext**
   - **AES Ciphertext**
   - **HKDF Salt**
   - **GCM Auth Tag**
   - **GCM IV**

### Decryption Process

1. **Decapsulation:**  
   The recipient decapsulates the Kyber ciphertext using their private key to recover the shared secret.

2. **Key Recovery:**  
   Using the recovered shared secret and the stored **HKDF Salt**, the 256-bit AES key and IV are re-derived.

3. **Authenticated Decryption:**  
   The message is decrypted via AES-256-GCM. The **Auth Tag** is verified; if the message was altered by even a single bit, the system rejects it.

### For Your Info

- **Post-Quantum Resilience:** Kyber KEM is a Round 3 NIST finalist, designed to resist attacks from future quantum computers.
- **Perfect Forward Secrecy:** Per-message fresh keypairs ensure that a compromise of one message doesn't affect others.
- **Authenticated Encryption:** AES-GCM prevents "Bit Flipping" attacks common in simpler modes.
- **Service Security:** The Python encryption service is hardened with an `ENCRYPTION_API_KEY` to prevent unauthorized access.

---

## 🤝 Contributing to the Chaos

This hell is solo-built by *Me | Muhammad Zeeshan Zafar* 🔥 but pull requests are welcome. If you're making major changes, open an issue first to discuss them.

**Remember:** If you're changing the abyss, update the tests accordingly!

---

## 📜 License

*📝 MIT License*

---

## 🚀 How to Run

### 1️⃣ **Initial Setup**

1. **Clone the Repository**
2. Run `composer install`  *(Ensure Composer is installed && run first time only)*
3. Run `npm install`  *(Ensure Node.js & npm are installed && run first time only)*
4. Configure your `.env` file and set up MySQL (⚠️ Avoid Apache port 8080)
5. Run `php artisan migrate --seed` (run first time only)
6. Start the Laravel server:  
   ```sh
   php artisan serve --host=0.0.0.0 --port=8001
   ```
7. Start Vite for the frontend:  
   ```sh
   npm run dev --host=0.0.0.0 --port=5173
   ```
8. Start WebSockets:  
   ```sh
   php artisan reverb:start --host=0.0.0.0 --port=8080
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
    cmake --build liboqs/build --target install  | (run with sudo on Linux)
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
21. Generate self-signed certificate and private key via OpenSSL for using https:
    ```sh
    openssl req -x509 -newkey rsa:4096 -nodes -out cert.pem -keyout key.pem -days 365 
    ```
22. **Run the encryption server:**
    Ensure the `ENCRYPTION_API_KEY` in your `.env` matches the key used here.
    ```sh
    # Set the key and run
    export ENCRYPTION_API_KEY=your_secret_key
    python encryption_service.py
    ```

---

### 3️⃣ **Technical Dependencies**

- **Python**: Requires `cryptography>=44.0.0` for HKDF support.
- **Node.js**: Requires `@inertiajs/vue3` pinned to `^2.3.0` for Inertia v1.x compatibility.
- **PHP**: Requires `curl` and `openssl` extensions.
- **PQC**: Requires `liboqs` and `liboqs-python` correctly built and linked.


---

## 🕵️‍♂️ LOCAL HTTPS / Nginx SETUP for Linux (Optional)    

Follow these steps to host **Knull-V0id** **over HTTPS on your local server**:  

> **This step is only needed if you are setting up the void on your localhost and plan to expose your IP for public access OR if you are hosting the void on a VPS and need to set up HTTPS locally.**  

### **1️⃣ Install Nginx & Generate SSL Certificates**  

```sh
sudo apt update
sudo apt install nginx -y
sudo openssl req -x509 -newkey rsa:4096 -keyout /etc/nginx/cert.key -out /etc/nginx/cert.crt -days 365 -nodes
```

---

### **2️⃣ Configure Nginx**  

1. Open the Nginx config file for editing:  
   ```sh
   sudo nano /etc/nginx/sites-available/knullvoid
   ```

2. Add the following configuration in the file:  

   ```nginx
   server {
       listen 443 ssl;
       server_name localhost;

       ssl_certificate /etc/nginx/cert.crt;
       ssl_certificate_key /etc/nginx/cert.key;

       location / {
           proxy_pass http://127.0.0.1:8001;
           proxy_set_header Host $host;
           proxy_set_header X-Real-IP $remote_addr;
           proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
           proxy_set_header X-Forwarded-Proto $scheme;
       }
   }

   server {
       listen 80;
       server_name localhost;
       return 301 https://localhost$request_uri;
   }
   ```

3. **Enable the Nginx Configuration**:  
   ```sh
   sudo ln -s /etc/nginx/sites-available/knullvoid /etc/nginx/sites-enabled/
   ```

4. **Restart Nginx to Apply Changes**:  
   ```sh
   sudo systemctl restart nginx
   ```

5. **Add the following in your resources/js/bootstrap.js file:**:  
   ```sh
   axios.defaults.baseURL = import.meta.env.VITE_APP_URL + ':8001';
   ```

---

### **3️⃣ Restart Laravel Server**  

Now, restart your Laravel application:  

```sh
php artisan serve --host=0.0.0.0 --port=8001
```

---

**🎉 Done!** You can now access Knull-V0id securely at:  
🔗 **https://localhost:8001**


🚨 **Note:** If you run into installation issues in oqs, please refer to the official [liboqs-python GitHub repository](https://github.com/open-quantum-safe/liboqs-python).
Windows Fellaws may need to manually install additional stuff like gcc, MingW, Doxygen, cmake, cmake build tools, visual studio build tools etc and set environment varibales for them.

---

🎉 **You're all set! Welcome to the abyss...**  
Access the void at: [http://localhost:8001](http://localhost:8001)
(God Panel will be at http://localhost:8001/god-board, get the initial creds from DummyUserSeeder)

## 📬 Email Verification

Void Verfies Emails. When a user registers, they receive an email from the abyss, after which they can login to the hell. Configure mail details in .env file to obtain the abyss's email.

---

## 🕵️‍♂️ TOR SETUP (Linux)  

Follow these steps to host **Knull-V0id** on the **Tor/Onion Network**:

### 1️⃣ **Modify Configuration Files**  

1. **Update Vite Config:**  
   - Open `vite.config.js`  
   - **Comment out** the current `export`  
   - **Uncomment** the **hidden service export**  

2. **Enable Onion Middleware:**  
   - Open `bootstrap/app.php`  
   - Uncomment:  
     ```php
     \App\Http\Middleware\ForceOnionUrls::class
     ```

### 2️⃣ **Install & Configure Tor**  

3. **Install Tor:**  
   ```sh
   sudo apt-get install tor
   ```

4. **Edit Tor Configuration:**  
   ```sh
   sudo nano /etc/tor/torrc
   ```
   - Add the following at the **end** of the file:  
     ```
     # Define a hidden service for Knull-Void
     HiddenServiceDir /var/lib/tor/hidden_service/
     HiddenServicePort 80 127.0.0.1:8001
     HiddenServicePort 5173 127.0.0.1:5173
     HiddenServicePort 8080 127.0.0.1:8080

     ```

5. **Restart Tor Service:**  
   ```sh
   sudo systemctl restart tor
   ```

### 3️⃣ **Obtain Your Onion Address**  

6. **Get the Generated Onion Address:**  
   ```sh
   sudo cat /var/lib/tor/hidden_service/hostname
   ```
   - This will print the **.onion** address for **Knull-Void**.

7. **Update Environment Variables:**  
   Edit your `.env` file and set the generated onion address from above step here:  
   ```ini
   APP_URL=http://xyzEXAMPLE123.onion
   VITE_APP_ONION_DOMAIN=xyzEXAMPLE123.onion
   VITE_APP_URL="${APP_URL}"
   ```

### 4️⃣ **Build & Restart the Server**  

8. **Build the Frontend:**  
   ```sh
   npm run build
   ```

9. **Restart Services:**  
   - Stop `npm run dev` & `php artisan serve` if they are running.  
   - Restart Laravel & Vite using the correct **ports & hosts** from Step 6 & 7.

10. **Clear Cache & Config:**  
    ```sh
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    ```

### 5️⃣ **Final Steps**  

11. **Open Tor Browser:**  
    - **Disable HTTPS-Only Mode**  
    - **Reduce security to standard**  

12. **Visit the .onion Address**  
    - Paste the **.onion** address from Step 6 into the browser.  

---

### 🎉 **KABOOM! Void is now hosted on Onion!** 🎭


## 🛡️ Reach the Hell Master

- **LinkedIn:** [https://www.linkedin.com/in/mzeeshanzafar28/](https://www.linkedin.com/in/m-zeeshanzafar28/)
- **Instagram:** [https://www.instagram.com/mzeeshanzafar28/](https://www.instagram.com/mzeeshanzafar28/)
- **Portfolio1:** [https://mzeeshanzafar28.github.io/zee_folio/](https://mzeeshanzafar28.github.io/zee_folio/)
- **Portfolio2:** [https://mzeeshanzafar28.github.io/zeefolio/](https://mzeeshanzafar28.github.io/zeefolio/)
- **TryHackMe:** [https://tryhackme.com/p/mzeeshanzafar28](https://tryhackme.com/p/mzeeshanzafar28)
- **HackerRank:** [https://www.hackerrank.com/profile/mzeeshanzafar28](https://www.hackerrank.com/profile/mzeeshanzafar28)
- **W3Schools:** [https://www.w3profile.com/mzeeshanzafar28](https://www.w3profile.com/mzeeshanzafar28)

---



---
