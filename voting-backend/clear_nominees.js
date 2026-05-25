import { pool } from "./db.js";

async function main() {
  try {
    console.log("Connecting to MySQL and clearing all demo nominees...");
    const [result] = await pool.query("DELETE FROM voting_nominees");
    console.log(`Success! All demo nominees cleared from voting_nominees. (Affected rows: ${result.affectedRows})`);
  } catch (error) {
    console.error("Database operation failed:", error.message);
  } finally {
    await pool.end();
    process.exit(0);
  }
}

main();
