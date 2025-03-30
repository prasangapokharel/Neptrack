<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Daily Protein Tracker</title>
  <!-- Tailwind CSS CDN -->
  <link href="https://cdn.tailwindcss.com" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              DEFAULT: '#ff6b45',
              dark: '#e74c3c',
              light: '#ff8c6f'
            }
          },
          fontFamily: {
            sans: ['Inter', 'system-ui', 'sans-serif']
          }
        }
      }
    }
  </script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap');
    
    body {
      background-color: #1f2125;
      color: #ffffff;
      font-family: 'Inter', sans-serif;
      font-weight: 300;
      min-height: 100vh;
    }
    
    .glass {
      background-color: #000;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    .glass-dark {
      background-color: #1f2125;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    input, select, textarea {
      background-color: rgba(255, 255, 255, 0.1) !important;
      border-color: rgba(255, 255, 255, 0.2) !important;
      color: white !important;
    }
    
    input::placeholder, select::placeholder, textarea::placeholder {
      color: rgba(255, 255, 255, 0.5) !important;
    }
    
    input:focus, select:focus, textarea:focus {
      border-color: rgba(255, 255, 255, 0.5) !important;
      box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.2) !important;
    }
  </style>
</head>
<body>
  <!-- Admin Header -->
  <header class="glass-dark border-b border-white/10 mb-6">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      <div class="flex items-center">
        <a href="/admin/dashboard" class="text-xl font-medium text-white flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
          </svg>
          Admin Panel
        </a>
      </div>
      
      <nav class="flex items-center space-x-6">
        <a href="/admin/dashboard" class="text-white/70 hover:text-white transition duration-200">Dashboard</a>
        <a href="/admin/workouts" class="text-white/70 hover:text-white transition duration-200">Workouts</a>
        <a href="/admin/users" class="text-white/70 hover:text-white transition duration-200">Users</a>
        <a href="/" class="text-white/70 hover:text-white transition duration-200">View Site</a>
        <a href="/admin/logout" class="bg-red-500/20 hover:bg-red-500/30 text-red-300 px-4 py-2 rounded-lg text-sm transition duration-200">Logout</a>
      </nav>
    </div>
  </header>
  <script src="/assets/js/tailwind.js"></script>

  <div class="container mx-auto px-4 py-6">


