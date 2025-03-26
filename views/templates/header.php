<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daily Protein Tracker</title>
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
<div class="container mx-auto px-4 py-6">

