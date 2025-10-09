console.log("✅ script.js loaded");

document.getElementById("loginForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;

  console.log("Username input:", username);
  console.log("Password input:", password);

  try {
    const res = await fetch("http://localhost:8000/auth.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username, password })
    });

    const data = await res.json();
    console.log("Response from server:", data);

    const result = document.getElementById("result");

    if (data.success) {
      result.textContent = "✅ " + data.message;
      result.className = "text-green-600 text-center mt-4";

      setTimeout(() => {
        if (data.role && data.role.toLowerCase() === "admin") {
          window.location.href = "LandingPageAdmin.html";
        } else {
          window.location.href = "LandingPage.html";
        }
      }, 1000);
    } else {
      result.textContent = "❌ " + data.message;
      result.className = "text-red-600 text-center mt-4";
    }
  } catch (err) {
    console.error("Fetch error:", err);
  }
});
