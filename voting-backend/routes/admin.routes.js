import jwt from "jsonwebtoken";
import { Router } from "express";
import { pool } from "../db.js";
import { config } from "../config.js";

const router = Router();

const adminAuth = (req, res, next) => {
  const header = req.headers.authorization || "";
  const token = header.startsWith("Bearer ") ? header.slice(7) : null;

  if (!token) {
    res.status(401).json({ message: "Admin token required." });
    return;
  }

  try {
    req.user = jwt.verify(token, config.admin.jwtSecret);
    next();
  } catch (_error) {
    res.status(401).json({ message: "Admin token is invalid or expired." });
  }
};

const getSettingMap = async () => {
  const [rows] = await pool.query(
    `SELECT setting_key, setting_value
     FROM voting_settings`,
  );

  return rows.reduce((acc, row) => {
    acc[row.setting_key] = row.setting_value;
    return acc;
  }, {});
};

const saveSettings = async (entries) => {
  for (const [key, value] of Object.entries(entries)) {
    await pool.query(
      `INSERT INTO voting_settings (setting_key, setting_value)
       VALUES (?, ?)
       ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP`,
      [key, value],
    );
  }
};

router.post("/login", async (req, res) => {
  const { username, password } = req.body;

  if (username !== config.admin.username || password !== config.admin.password) {
    res.status(401).json({ message: "Invalid admin credentials." });
    return;
  }

  const token = jwt.sign(
    {
      id: "voting-admin",
      name: config.admin.displayName,
      role: "admin",
      username: config.admin.username,
    },
    config.admin.jwtSecret,
    { expiresIn: "12h" },
  );

  res.json({
    status: "success",
    token,
    user: {
      id: "voting-admin",
      name: config.admin.displayName,
      role: "admin",
      username: config.admin.username,
    },
  });
});

router.use(adminAuth);

router.get("/dashboard", async (_req, res, next) => {
  try {
    const [categoryRows] = await pool.query(
      `SELECT c.id, c.slug, c.name, c.group_key, c.group_name, c.vote_price, c.is_active,
              COUNT(DISTINCT CASE WHEN n.is_active = 1 THEN n.id END) AS nomineeCount,
              COALESCE(SUM(CASE WHEN t.status = 'confirmed' THEN t.votes ELSE 0 END), 0) AS totalVotes,
              COALESCE(SUM(CASE WHEN t.status = 'confirmed' THEN t.amount ELSE 0 END), 0) AS totalAmount
       FROM voting_categories c
       LEFT JOIN voting_nominees n ON n.category_id = c.id
       LEFT JOIN voting_transactions t ON t.nominee_id = n.id
       GROUP BY c.id
       ORDER BY c.group_sort ASC, c.sort_order ASC, c.name ASC`,
    );

    const [leaderRows] = await pool.query(
      `SELECT c.id AS categoryId, n.id, n.full_name, c.name AS categoryName,
              COALESCE(SUM(CASE WHEN t.status = 'confirmed' THEN t.votes ELSE 0 END), 0) AS totalVotes
       FROM voting_categories c
       LEFT JOIN voting_nominees n ON n.category_id = c.id AND n.is_active = 1
       LEFT JOIN voting_transactions t ON t.nominee_id = n.id
       GROUP BY c.id, n.id
       ORDER BY c.id ASC, totalVotes DESC`,
    );

    const [totals] = await pool.query(
      `SELECT
         COALESCE(SUM(CASE WHEN status = 'confirmed' THEN votes ELSE 0 END), 0) AS totalVotes,
         COALESCE(SUM(CASE WHEN status = 'confirmed' THEN amount ELSE 0 END), 0) AS totalAmount,
         COUNT(CASE WHEN status = 'confirmed' THEN 1 END) AS confirmedTransactions
       FROM voting_transactions`,
    );

    const [sections] = await pool.query(
      `SELECT id, section_key, section_name, description, flyer_image_url, folder_name, sort_order, is_active
       FROM voting_sections
       ORDER BY sort_order ASC, section_name ASC`,
    );

    const settings = await getSettingMap();
    const leadersByCategory = new Map();
    for (const row of leaderRows) {
      if (!row.id || leadersByCategory.has(row.categoryId)) {
        continue;
      }
      leadersByCategory.set(row.categoryId, row);
    }

    res.json({
      totals: {
        totalVotes: Number(totals[0]?.totalVotes || 0),
        totalAmount: Number(totals[0]?.totalAmount || 0),
        confirmedTransactions: Number(totals[0]?.confirmedTransactions || 0),
      },
      settings,
      sections,
      categories: categoryRows.map((row) => ({
        ...row,
        nomineeCount: Number(row.nomineeCount || 0),
        totalVotes: Number(row.totalVotes || 0),
        totalAmount: Number(row.totalAmount || 0),
        leader: leadersByCategory.get(row.id) || null,
      })),
    });
  } catch (error) {
    next(error);
  }
});

router.get("/settings", async (_req, res, next) => {
  try {
    res.json(await getSettingMap());
  } catch (error) {
    next(error);
  }
});

router.put("/settings", async (req, res, next) => {
  try {
    await saveSettings(req.body || {});
    res.json(await getSettingMap());
  } catch (error) {
    next(error);
  }
});

router.get("/sections", async (_req, res, next) => {
  try {
    const [rows] = await pool.query(
      `SELECT id, section_key, section_name, description, flyer_image_url, folder_name, sort_order, is_active
       FROM voting_sections
       ORDER BY sort_order ASC, section_name ASC`,
    );
    res.json(rows);
  } catch (error) {
    next(error);
  }
});

router.post("/sections", async (req, res, next) => {
  try {
    const { section_key, section_name, description, flyer_image_url, folder_name, sort_order, is_active } = req.body;
    await pool.query(
      `INSERT INTO voting_sections
       (section_key, section_name, description, flyer_image_url, folder_name, sort_order, is_active)
       VALUES (?, ?, ?, ?, ?, ?, ?)`,
      [
        section_key,
        section_name,
        description || null,
        flyer_image_url || null,
        folder_name || null,
        Number.parseInt(String(sort_order || 0), 10) || 0,
        is_active === false ? 0 : 1,
      ],
    );
    const [rows] = await pool.query(`SELECT * FROM voting_sections ORDER BY sort_order ASC, section_name ASC`);
    res.json(rows);
  } catch (error) {
    next(error);
  }
});

router.put("/sections/:id", async (req, res, next) => {
  try {
    const { section_key, section_name, description, flyer_image_url, folder_name, sort_order, is_active } = req.body;
    await pool.query(
      `UPDATE voting_sections
       SET section_key = ?, section_name = ?, description = ?, flyer_image_url = ?, folder_name = ?,
           sort_order = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP
       WHERE id = ?`,
      [
        section_key,
        section_name,
        description || null,
        flyer_image_url || null,
        folder_name || null,
        Number.parseInt(String(sort_order || 0), 10) || 0,
        is_active === false ? 0 : 1,
        req.params.id,
      ],
    );
    const [rows] = await pool.query(`SELECT * FROM voting_sections ORDER BY sort_order ASC, section_name ASC`);
    res.json(rows);
  } catch (error) {
    next(error);
  }
});

router.delete("/sections/:id", async (req, res, next) => {
  try {
    await pool.query(`DELETE FROM voting_sections WHERE id = ?`, [req.params.id]);
    res.json({ status: "success" });
  } catch (error) {
    next(error);
  }
});

router.get("/categories", async (_req, res, next) => {
  try {
    const [rows] = await pool.query(
      `SELECT *
       FROM voting_categories
       ORDER BY group_sort ASC, sort_order ASC, name ASC`,
    );
    res.json(rows);
  } catch (error) {
    next(error);
  }
});

router.post("/categories", async (req, res, next) => {
  try {
    const { slug, name, description, group_key, group_name, group_sort, accent_color, hero_image, vote_price, sort_order, is_active } = req.body;
    await pool.query(
      `INSERT INTO voting_categories
       (slug, name, description, group_key, group_name, group_sort, accent_color, hero_image, vote_price, sort_order, is_active)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        slug,
        name,
        description,
        group_key,
        group_name,
        Number.parseInt(String(group_sort || 0), 10) || 0,
        accent_color || "#0f9d58",
        hero_image || null,
        Number.parseInt(String(vote_price || config.voting.votePrice), 10) || config.voting.votePrice,
        Number.parseInt(String(sort_order || 0), 10) || 0,
        is_active === false ? 0 : 1,
      ],
    );
    const [rows] = await pool.query(`SELECT * FROM voting_categories ORDER BY group_sort ASC, sort_order ASC, name ASC`);
    res.json(rows);
  } catch (error) {
    next(error);
  }
});

router.put("/categories/:id", async (req, res, next) => {
  try {
    const { slug, name, description, group_key, group_name, group_sort, accent_color, hero_image, vote_price, sort_order, is_active } = req.body;
    await pool.query(
      `UPDATE voting_categories
       SET slug = ?, name = ?, description = ?, group_key = ?, group_name = ?, group_sort = ?, accent_color = ?,
           hero_image = ?, vote_price = ?, sort_order = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP
       WHERE id = ?`,
      [
        slug,
        name,
        description,
        group_key,
        group_name,
        Number.parseInt(String(group_sort || 0), 10) || 0,
        accent_color || "#0f9d58",
        hero_image || null,
        Number.parseInt(String(vote_price || config.voting.votePrice), 10) || config.voting.votePrice,
        Number.parseInt(String(sort_order || 0), 10) || 0,
        is_active === false ? 0 : 1,
        req.params.id,
      ],
    );
    const [rows] = await pool.query(`SELECT * FROM voting_categories ORDER BY group_sort ASC, sort_order ASC, name ASC`);
    res.json(rows);
  } catch (error) {
    next(error);
  }
});

router.delete("/categories/:id", async (req, res, next) => {
  try {
    await pool.query(`DELETE FROM voting_categories WHERE id = ?`, [req.params.id]);
    res.json({ status: "success" });
  } catch (error) {
    next(error);
  }
});

router.get("/nominees", async (_req, res, next) => {
  try {
    const [rows] = await pool.query(
      `SELECT n.*, c.name AS category_name, c.slug AS category_slug,
              COALESCE(SUM(CASE WHEN t.status = 'confirmed' THEN t.votes ELSE 0 END), 0) AS votes
       FROM voting_nominees n
       INNER JOIN voting_categories c ON c.id = n.category_id
       LEFT JOIN voting_transactions t ON t.nominee_id = n.id
       GROUP BY n.id
       ORDER BY c.group_sort ASC, c.sort_order ASC, n.full_name ASC`,
    );
    res.json(rows);
  } catch (error) {
    next(error);
  }
});

router.post("/nominees", async (req, res, next) => {
  try {
    const { category_id, slug, full_name, department, level_label, bio, photo_url, is_active } = req.body;
    await pool.query(
      `INSERT INTO voting_nominees
       (category_id, slug, full_name, department, level_label, bio, photo_url, is_active)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        category_id,
        slug,
        full_name,
        department,
        level_label,
        bio,
        photo_url || null,
        is_active === false ? 0 : 1,
      ],
    );
    const [rows] = await pool.query(
      `SELECT n.*, c.name AS category_name, c.slug AS category_slug,
              COALESCE(SUM(CASE WHEN t.status = 'confirmed' THEN t.votes ELSE 0 END), 0) AS votes
       FROM voting_nominees n
       INNER JOIN voting_categories c ON c.id = n.category_id
       LEFT JOIN voting_transactions t ON t.nominee_id = n.id
       GROUP BY n.id
       ORDER BY c.group_sort ASC, c.sort_order ASC, n.full_name ASC`,
    );
    res.json(rows);
  } catch (error) {
    next(error);
  }
});

router.put("/nominees/:id", async (req, res, next) => {
  try {
    const { category_id, slug, full_name, department, level_label, bio, photo_url, is_active } = req.body;
    await pool.query(
      `UPDATE voting_nominees
       SET category_id = ?, slug = ?, full_name = ?, department = ?, level_label = ?, bio = ?, photo_url = ?, is_active = ?,
           updated_at = CURRENT_TIMESTAMP
       WHERE id = ?`,
      [
        category_id,
        slug,
        full_name,
        department,
        level_label,
        bio,
        photo_url || null,
        is_active === false ? 0 : 1,
        req.params.id,
      ],
    );
    const [rows] = await pool.query(
      `SELECT n.*, c.name AS category_name, c.slug AS category_slug,
              COALESCE(SUM(CASE WHEN t.status = 'confirmed' THEN t.votes ELSE 0 END), 0) AS votes
       FROM voting_nominees n
       INNER JOIN voting_categories c ON c.id = n.category_id
       LEFT JOIN voting_transactions t ON t.nominee_id = n.id
       GROUP BY n.id
       ORDER BY c.group_sort ASC, c.sort_order ASC, n.full_name ASC`,
    );
    res.json(rows);
  } catch (error) {
    next(error);
  }
});

router.delete("/nominees/:id", async (req, res, next) => {
  try {
    await pool.query(`DELETE FROM voting_nominees WHERE id = ?`, [req.params.id]);
    res.json({ status: "success" });
  } catch (error) {
    next(error);
  }
});

export default router;
