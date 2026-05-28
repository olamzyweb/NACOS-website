#!/bin/bash
# ============================================================
# NACOS Voting Backend - cPanel SSH Deploy Script
# Run this once you have SSH access to the server
# Usage: bash deploy.sh
# ============================================================

set -e  # Exit immediately if any command fails

APP_DIR="/home/starbrig/api-voting.nacoslasustech.org.ng"

echo ""
echo "=================================================="
echo "  🗳️  NACOS Voting Backend - SSH Deployer"
echo "=================================================="
echo ""

# Step 1: Navigate to app directory
echo "📁 Step 1: Navigating to app directory..."
cd "$APP_DIR" || { echo "❌ Directory not found: $APP_DIR"; exit 1; }
echo "✅ In: $(pwd)"
echo ""

# Step 2: Check Node.js version
echo "🟢 Step 2: Checking Node.js..."
node -v || { echo "❌ Node.js not found. Make sure Node.js 16.x is selected in cPanel."; exit 1; }
npm -v
echo ""

# Step 3: Install dependencies
echo "📦 Step 3: Installing dependencies..."
npm install --legacy-peer-deps --no-audit --no-fund --prefer-offline 2>&1 || \
npm install --force --no-audit --no-fund 2>&1 || \
{ echo "❌ npm install failed. Try manually running: npm install --legacy-peer-deps"; exit 1; }
echo "✅ Dependencies installed!"
echo ""

# Step 4: Verify .env file exists
echo "🔐 Step 4: Checking .env file..."
if [ ! -f ".env" ]; then
  echo "⚠️  .env file not found! Creating from .env.example..."
  if [ -f ".env.example" ]; then
    cp .env.example .env
    echo "✅ .env created from .env.example — PLEASE EDIT IT with real values!"
  else
    echo "❌ No .env or .env.example found. Please upload your .env file."
    exit 1
  fi
else
  echo "✅ .env file found!"
fi
echo ""

# Step 5: Verify critical files exist
echo "📋 Step 5: Verifying required files..."
FILES=("index.js" "app.js" "config.js" "db.js" "package.json")
for f in "${FILES[@]}"; do
  if [ -f "$f" ]; then
    echo "  ✅ $f"
  else
    echo "  ❌ MISSING: $f — please upload this file!"
  fi
done
echo ""

# Step 6: Test the app starts correctly (quick check, then kill)
echo "🧪 Step 6: Quick start test (5 seconds)..."
timeout 5 node index.js 2>&1 || true
echo ""

# Step 7: Import SQL schema (optional)
echo "🗄️  Step 7: Database schema info"
if [ -f "sql/schema.sql" ]; then
  echo "✅ schema.sql found at: $APP_DIR/sql/schema.sql"
  echo "   → Import it via cPanel phpMyAdmin into your database."
  echo "   → Or run: mysql -u DB_USER -p DB_NAME < sql/schema.sql"
else
  echo "⚠️  No sql/schema.sql found."
fi
echo ""

echo "=================================================="
echo "  ✅ Deploy script complete!"
echo ""
echo "  Next steps:"
echo "  1. Edit .env with your real DB credentials"
echo "  2. Import sql/schema.sql into your MySQL database"
echo "  3. Go to cPanel → Setup Node.js App"
echo "     - Startup file: app.js"
echo "     - Node version: 16.x"
echo "     - Click Restart (NOT 'Run NPM Install')"
echo "  4. Visit https://api-voting.nacoslasustech.org.ng"
echo "=================================================="
echo ""
