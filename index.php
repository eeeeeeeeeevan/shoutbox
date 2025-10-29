<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>shout</title>
    <script src="./assets/tw.js"></script>
    <link rel="stylesheet" href="./assets/main.css">
</head>

<body>
    <div>
        <aside>
            <div>
                <h2>shout</h2>

                <form action="shout.php" method="post" id="primary">

                    <div>
                        <label for="name">
                            name
                        </label>
                        <input type="text" name="name" id="name" value="anonymous user" placeholder="enter name">
                    </div>

                    <div>
                        <label for="post">
                            msg
                        </label>
                        <textarea name="post" id="post" rows="4" placeholder="type a message" required></textarea>
                    </div>

                    <button type="button" onclick="submitchal()" id="subbtn">
                        create shout
                    </button>

                    <div id="powstat" class="hidden">
                        <span id="powmsg">wait..</span>
                        <div id="hash-display" class="hidden">
                            <div>
                                <span>current hash:</span>
                                <span id="chash">...</span>
                            </div>
                            <div style="display:none;">
                                <span>target:</span>
                                <span id="targ">0</span>
                                <span>...</span>
                            </div>
                            <div>
                                <span>nonce:</span>
                                <span id="nonce">0</span>
                            </div>
                            <div>
                                <span>challenge:</span>
                                <span id="currchal">...</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div>
                <p>
                    powered by
                    <a href="https://evan.lat">this person</a>.
                </p>
            </div>
        </aside>

        <main>
            <div>
                <div id="posts-container">
                    <!-- yeah fuck you -->
                    <?php echo file_get_contents("posts.html"); ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        async function sha256(e) { const t = (new TextEncoder).encode(e), n = await crypto.subtle.digest("SHA-256", t); return Array.from(new Uint8Array(n)).map((e => e.toString(16).padStart(2, "0"))).join("") } async function solvepow(e, t) { let n = 0; const a = "0".repeat(t), o = document.getElementById("chash"), s = document.getElementById("targ"), d = document.getElementById("nonce"), c = document.getElementById("currchal"); s.textContent = a, c.textContent = e.substring(0, 16) + "..."; let l = 0; for (; ;) { const t = n.toString(), s = await sha256(e + t); if (l % 100 == 0 && (o.textContent = s, d.textContent = n, s.startsWith(a) ? o.className = "text-green-400 font-bold" : o.className = "text-si-foreground", await new Promise((e => setTimeout(e, 0)))), s.startsWith(a)) return o.textContent = s, d.textContent = n, o.className = "text-green-400 font-bold", t; n++, l++ } } async function submitchal() { const e = document.getElementById("primary"), t = document.getElementById("subbtn"), n = document.getElementById("powstat"), a = document.getElementById("powmsg"); if (e.post.value.trim()) { t.disabled = !0, t.classList.add("cranking"), n.classList.remove("hidden"), a.textContent = "requesting challenge..."; try { const t = await fetch("challenge.php", { method: "POST", headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: "action=get_challenge" }); if (!t.ok) throw new Error("Failed to get challenge"); const n = await t.json(); a.textContent = "calculating POW", document.getElementById("hash-display").classList.remove("hidden"); const o = await solvepow(n.challenge, n.difficulty), s = document.createElement("input"); s.type = "hidden", s.name = "challenge_id", s.value = n.id, e.appendChild(s); const d = document.createElement("input"); d.type = "hidden", d.name = "pow_solution", d.value = o, e.appendChild(d), e.submit() } catch (e) { console.error("wtf", e), t.disabled = !1, t.classList.remove("cranking"), n.classList.add("hidden"), document.getElementById("hash-display").classList.add("hidden") } } else alert("enter a message bro") }
    </script>
</body>

</html>