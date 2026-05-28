import { updateSetting } from './routes/admin.routes.js';
import mysql from 'mysql2/promise';
import { config } from './config.js';

async function fixDate() {
  const connection = await mysql.createConnection(config.db);
  await connection.query(
    "INSERT INTO voting_settings (setting_key, setting_value) VALUES ('votingClosesAt', '2026-06-11T00:00:00+01:00') ON DUPLICATE KEY UPDATE setting_value = '2026-06-11T00:00:00+01:00'"
  );
  await connection.query(
    "INSERT INTO voting_settings (setting_key, setting_value) VALUES ('awardsDate', '2026-06-11T00:00:00+01:00') ON DUPLICATE KEY UPDATE setting_value = '2026-06-11T00:00:00+01:00'"
  );
  console.log("Dates fixed in DB!");
  process.exit(0);
}

fixDate().catch(console.error);
