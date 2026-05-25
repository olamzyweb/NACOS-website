async function run() {
  try {
    const res = await fetch("http://localhost:5050/api/nominees/tmb");
    console.log("Status:", res.status);
    const data = await res.json();
    console.log("Data:", data);
  } catch (err) {
    console.error("Fetch error:", err);
  }
}

run();
