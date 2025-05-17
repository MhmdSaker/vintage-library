<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logo - Vintage Library</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="src/styles/main.css">
    <!-- Update Lucide script to latest version -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        .logo-container {
            min-height: 70vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .logo-wrapper {
            text-align: center;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .logo-wrapper:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 25px rgba(139, 115, 85, 0.2);
        }

        .logo-icon {
            width: 120px;
            height: 120px;
            color: #8b7355;
            margin-bottom: 1.5rem;
        }

        .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: #2d2d2d;
            margin-bottom: 1rem;
        }

        .logo-description {
            font-family: 'Libre Baskerville', serif;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark custom-nav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.html">
                <i data-lucide="library" class="me-2"></i>
                <span class="font-playfair">Vintage Library</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link px-2" href="index.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Home">
                            <i data-lucide="home"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="search-results.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Browse Books">
                            <i data-lucide="book-open"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="categories.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Categories">
                            <i data-lucide="list"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="events.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Events">
                            <i data-lucide="calendar"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="about.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="About">
                            <i data-lucide="info"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="contact.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Contact">
                            <i data-lucide="mail"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Logo Section -->
    <main class="container logo-container">
        <div class="logo-wrapper">
            <i data-lucide="library" class="logo-icon"></i>
            <h1 class="logo-text">Vintage Library</h1>
            <p class="logo-description">Preserving and sharing timeless literary masterpieces with readers around the world.</p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="footer-bottom py-3">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="mb-0">&copy; 2024 Vintage Library. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p class="mb-0">Designed with <i data-lucide="heart" class="text-danger mx-1"></i> for book lovers</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html> 