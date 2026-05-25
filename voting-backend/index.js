import cors from "cors";
import express from "express";
import helmet from "helmet";
import morgan from "morgan";
import rateLimit from "express-rate-limit";

import { config } from "./config.js";
import adminRoutes from "./routes/admin.routes.js";
import votingRoutes from "./routes/voting.routes.js";

const app = express();

app.set("trust proxy", 1);
app.use(helmet());
app.use(cors({ origin: "*" }));
app.use(express.json({ limit: "1mb" }));
app.use(morgan("dev"));
app.use(
  rateLimit({
    windowMs: 15 * 60 * 1000,
    max: 200,
    message: "Too many requests. Please try again later.",
  }),
);

app.get("/", (_req, res) => {
  res.send("NACOS voting backend is running.");
});

app.get("/api/health", (_req, res) => {
  res.json({
    status: "up",
    timestamp: new Date().toISOString(),
  });
});

app.use("/api/voting", votingRoutes);
app.use("/api", votingRoutes);
app.use("/api/admin", adminRoutes);

app.use((error, _req, res, _next) => {
  console.error("Voting backend error:", error);
  res.status(500).json({
    message: error?.message || "Internal server error.",
  });
});

app.listen(config.port, () => {
  console.log(`NACOS voting backend running on http://localhost:${config.port}`);
});
