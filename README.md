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
7. Start Vite for frontend:  
   ```sh
   npm run dev
   ```
8. Start WebSockets:  
   ```sh
   php artisan reverb:start --port=8080
   ```
9. Start Queue Worker:  
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
    venv\Scripts\activate  # Windows
    ```
13. Install dependencies:
    ```sh
    pip install -r requirements.txt
    ```
14. Install **CMake**:
    ```sh
    sudo apt-get install cmake  # Linux/Mac
    choco install cmake  # Windows (requires Chocolatey)
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
17. Set Path:
    ```sh
    export LD_LIBRARY_PATH=$LD_LIBRARY_PATH:/usr/local/lib  # Linux
    set PATH=%PATH%;C:\Program Files (x86)\liboqs\bin  # Windows
    ```
18. Clone **liboqs-python**:
    ```sh
    git clone --depth=1 https://github.com/open-quantum-safe/liboqs-python
    ```
19. Install **liboqs-python**:
    ```sh
    cd liboqs-python
    python3 -m ensurepip --upgrade  # Windows: replace python3 with python
    pip install .
    cd ..
    ```
20. Verify installation:
    ```sh
    python3 liboqs-python/examples/kem.py  # Windows: replace python3 with python
    ```
21. **Run the encryption server:**
    ```sh
    python encryption_service.py
    ```

🚨 **Note:** If installation issues arise, refer to the official repo:  
🔗 [OQS Python GitHub](https://github.com/open-quantum-safe/liboqs-python)

---

🎉 **You're all set!** Access the void at:  
🔗 **[http://localhost:8001](http://localhost:8001)**

👁️ **Welcome to the abyss...**


---
## 🛡️Reach the Hell Master
**Linkedin:** https://www.linkedin.com/in/m-zeeshan-zafar-9205a1248/

**Instagram:** https://www.instagram.com/mzeeshanzafar28/

**Portfolio1:** https://mzeeshanzafar28.github.io/zee_folio/

**Portfolio2:** https://mzeeshanzafar28.github.io/zeefolio/

**TryHackMe:** https://tryhackme.com/p/mzeeshanzafar28

**HackerRank:** https://www.hackerrank.com/profile/mzeeshanzafar28

**W3Schools:** https://www.w3profile.com/mzeeshanzafar28
