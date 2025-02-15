#!/bin/bash

gnome-terminal -- bash -c "php artisan serve --port=8001; exec bash"
gnome-terminal -- bash -c "php artisan reverb:start --port=8080; exec bash"
gnome-terminal -- bash -c "php artisan queue:work; exec bash"
gnome-terminal -- bash -c "php artisan schedule:work; exec bash"
gnome-terminal -- bash -c "npm run dev; exec bash"
gnome-terminal -- bash -c "source venv/bin/activate && python encryption_service.py; exec bash"
