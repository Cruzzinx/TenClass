document.getElementById("loginForm").addEventListener("submit", async (e) => {
    e.preventDefault();
  
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
  
    const res = await fetch("http://localhost/tenclass/auth.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username, password })
    });
  
    const data = await res.json();
    const result = document.getElementById("result");
  
    if (data.success) {
      result.textContent = "✅ " + data.message;
      result.className = "text-green-600 text-center mt-4";
      setTimeout(() => {
        window.location.href = "LandingPage.html";
      }, 1000);
    } else {
      result.textContent = "❌ " + data.message;
      result.className = "text-red-600 text-center mt-4";
    }
  });
  