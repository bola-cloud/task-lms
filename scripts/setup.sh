#!/bin/bash

# Exit on error
set -e

echo "🚀 Starting LMS Setup..."

# Copy .env if not exists
if [ ! -f .env ]; then
    echo "📄 Creating .env file..."
    cp .env.example .env
fi

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
docker compose exec app composer install

# Generate application key
echo "🔑 Generating application key..."
docker compose exec app php artisan key:generate

# Run migrations and seeders
echo "🗄️ Running migrations and seeders..."
docker compose exec app php artisan migrate:fresh --seed

# Install NPM dependencies and build assets
echo "🎨 Installing NPM dependencies and building assets..."
docker compose exec app npm install
docker compose exec app npm run build

# Run tests
echo "🧪 Running tests..."
docker compose exec app php artisan test

echo "✅ Setup complete! Application is running at http://localhost:8080"
