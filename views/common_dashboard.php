<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNest</title>
    <style>
        :root {
            --primary: #5D4037;
            --primary-light: #8D6E63;
            --secondary: #FF8F00;
            --text-light: #EFEBE9;
            --text-dark: #263238;
            --background: #F5F5F5;
            --card-bg: #FFFFFF;
            --shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background);
            color: var(--text-dark);
            line-height: 1.6;
        }
        
        header {
            background-color: var(--primary);
            padding: 20px 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            color: var(--text-light);
            font-size: 28px;
            font-weight: 700;
        }
        
        .logo-icon {
            width: 30px;
            height: 30px;
            margin-right: 10px;
            display: inline-block;
            position: relative;
        }
        
        .logo-icon::before {
            content: "📚";
            font-size: 24px;
        }
        
        .nav-links {
            display: flex;
            gap: 20px;
        }
        
        .nav-links a {
            color: var(--text-light);
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .nav-links a:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }
        
        .hero {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('/api/placeholder/1200/400');
            background-size: cover;
            background-position: center;
            height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: var(--text-light);
            text-align: center;
            padding: 0 20px;
        }
        
        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .hero p {
            font-size: 18px;
            max-width: 600px;
            margin-bottom: 30px;
        }
        
        .search-bar {
            width: 100%;
            max-width: 700px;
            margin: -30px auto 40px;
            position: relative;
            z-index: 10;
        }
        
        .search-bar form {
            display: flex;
            background-color: var(--card-bg);
            padding: 15px;
            border-radius: 50px;
            box-shadow: var(--shadow);
        }
        
        .search-bar input {
            flex: 1;
            padding: 12px 20px;
            border: none;
            outline: none;
            font-size: 16px;
            border-radius: 25px;
            color: var(--text-dark);
        }
        
        .search-bar button {
            background-color: var(--secondary);
            color: var(--text-light);
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            margin-left: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .search-bar button:hover {
            background-color: #F57C00;
            transform: translateY(-2px);
        }
        
        .search-icon {
            display: inline-block;
            width: 16px;
            height: 16px;
            position: relative;
        }
        
        .search-icon::before {
            content: "🔍";
            font-size: 14px;
        }
        
        .section-title {
            text-align: center;
            margin: 40px 0 30px;
            font-size: 28px;
            color: var(--primary);
            position: relative;
            padding-bottom: 15px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background-color: var(--secondary);
        }
        
        .categories {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            max-width: 1000px;
            margin: 0 auto 40px;
            padding: 0 20px;
        }
        
        .category {
            background-color: var(--card-bg);
            color: var(--primary);
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .category:hover, .category.active {
            background-color: var(--primary);
            color: var(--text-light);
        }
        
        .book-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 30px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .book-card {
            background-color: var(--card-bg);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        
        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.2);
        }
        
        .book-image {
            position: relative;
            overflow: hidden;
            height: 300px;
        }
        
        .book-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }
        
        .book-card:hover .book-image img {
            transform: scale(1.1);
        }
        
        .book-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            opacity: 0;
            transition: var(--transition);
            display: flex;
            align-items: flex-end;
            padding: 20px;
        }
        
        .book-card:hover .book-overlay {
            opacity: 1;
        }
        
        .book-actions {
            display: flex;
            gap: 10px;
        }
        
        .book-actions a {
            background-color: var(--secondary);
            color: var(--text-light);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: var(--transition);
            text-decoration: none;
        }
        
        .book-actions a:hover {
            background-color: var(--primary);
            transform: translateY(-5px);
        }
        
        .action-icon {
            font-size: 18px;
        }
        
        .book-info {
            padding: 20px;
            text-align: center;
        }
        
        .book-info h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: var(--primary);
            font-weight: 600;
        }
        
        .book-info p {
            color: #757575;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .book-rating {
            color: var(--secondary);
            margin-bottom: 15px;
        }
        
        .star {
            display: inline-block;
            width: 18px;
            height: 18px;
            position: relative;
        }
        
        .star::before {
            content: "★";
            font-size: 18px;
        }
        
        .star-half::before {
            content: "✦";
        }
        
        .star-empty::before {
            content: "☆";
        }
        
        .book-badge {
            display: inline-block;
            background-color: #E0F2F1;
            color: #00897B;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        footer {
            background-color: var(--primary);
            color: var(--text-light);
            padding: 50px 20px 20px;
            margin-top: 60px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .footer-column h3 {
            font-size: 20px;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background-color: var(--secondary);
        }
        
        .footer-column p, .footer-column a {
            color: #BEBEBE;
            margin-bottom: 10px;
            display: block;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .footer-column a:hover {
            color: var(--secondary);
            transform: translateX(5px);
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-links a {
            background-color: rgba(255, 255, 255, 0.1);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--text-light);
            transition: var(--transition);
        }
        
        .social-links a:hover {
            background-color: var(--secondary);
            transform: translateY(-5px);
        }
        
        .social-icon {
            font-size: 18px;
        }
        
        .copyright {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 14px;
            color: #BEBEBE;
        }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 36px;
            }
            
            .book-container {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }
            
            .search-bar form {
                flex-direction: column;
                border-radius: 15px;
            }
            
            .search-bar button {
                margin-left: 0;
                margin-top: 10px;
                width: 100%;
                justify-content: center;
            }
            
            header {
                flex-direction: column;
                padding: 15px;
            }
            
            .nav-links {
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <span class="logo-icon"></span> BookNest
        </div>
        <div class="nav-links">
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
    </header>
    
    <section class="hero">
        <h1>Discover Your Next Favorite Book</h1>
        <p>Explore thousands of books from bestselling authors to emerging talents</p>
    </section>
    
    <div class="search-bar">
        <form method="GET" action="search.php">
            <input type="text" name="searchTerm" placeholder="Search by title, author, or genre..." required>
            <button type="submit"><span class="search-icon"></span> Search</button>
        </form>
    </div>
    
    <div class="categories">
        <div class="category active">All Books</div>
        <div class="category">Fiction</div>
        <div class="category">Non-Fiction</div>
        <div class="category">Mystery</div>
        <div class="category">Science Fiction</div>
        <div class="category">Romance</div>
    </div>
    
    <h2 class="section-title">Popular Books</h2>
    
    <div class="book-container">
        <div class="book-card">
            <div class="book-image">
                <img src="/api/placeholder/400/600" alt="Book 1">
                <div class="book-overlay">
                    <div class="book-actions">
                        <a href="#"><span class="action-icon">❤</span></a>
                        <a href="#"><span class="action-icon">📑</span></a>
                        <a href="#"><span class="action-icon">ℹ</span></a>
                    </div>
                </div>
            </div>
            <div class="book-info">
                <h3>The Silent Echo</h3>
                <p>Sarah Johnson</p>
                <div class="book-rating">
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star star-half"></span>
                </div>
                <div class="book-badge">Fiction</div>
            </div>
        </div>
        
        <div class="book-card">
            <div class="book-image">
                <img src="/api/placeholder/400/600" alt="Book 2">
                <div class="book-overlay">
                    <div class="book-actions">
                        <a href="#"><span class="action-icon">❤</span></a>
                        <a href="#"><span class="action-icon">📑</span></a>
                        <a href="#"><span class="action-icon">ℹ</span></a>
                    </div>
                </div>
            </div>
            <div class="book-info">
                <h3>Journey to Elsewhere</h3>
                <p>Michael Rodriguez</p>
                <div class="book-rating">
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star star-empty"></span>
                </div>
                <div class="book-badge">Adventure</div>
            </div>
        </div>
        
        <div class="book-card">
            <div class="book-image">
                <img src="/api/placeholder/400/600" alt="Book 3">
                <div class="book-overlay">
                    <div class="book-actions">
                        <a href="#"><span class="action-icon">❤</span></a>
                        <a href="#"><span class="action-icon">📑</span></a>
                        <a href="#"><span class="action-icon">ℹ</span></a>
                    </div>
                </div>
            </div>
            <div class="book-info">
                <h3>Midnight Whispers</h3>
                <p>Emily Chen</p>
                <div class="book-rating">
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                </div>
                <div class="book-badge">Mystery</div>
            </div>
        </div>
        
        <div class="book-card">
            <div class="book-image">
                <img src="/api/placeholder/400/600" alt="Book 4">
                <div class="book-overlay">
                    <div class="book-actions">
                        <a href="#"><span class="action-icon">❤</span></a>
                        <a href="#"><span class="action-icon">📑</span></a>
                        <a href="#"><span class="action-icon">ℹ</span></a>
                    </div>
                </div>
            </div>
            <div class="book-info">
                <h3>The Quantum Key</h3>
                <p>David Peterson</p>
                <div class="book-rating">
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star star-half"></span>
                    <span class="star star-empty"></span>
                </div>
                <div class="book-badge">Sci-Fi</div>
            </div>
        </div>
        
        <div class="book-card">
            <div class="book-image">
                <img src="/api/placeholder/400/600" alt="Book 5">
                <div class="book-overlay">
                    <div class="book-actions">
                        <a href="#"><span class="action-icon">❤</span></a>
                        <a href="#"><span class="action-icon">📑</span></a>
                        <a href="#"><span class="action-icon">ℹ</span></a>
                    </div>
                </div>
            </div>
            <div class="book-info">
                <h3>Echoes of Yesterday</h3>
                <p>Lisa Thompson</p>
                <div class="book-rating">
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star star-empty"></span>
                </div>
                <div class="book-badge">Historical</div>
            </div>
        </div>
        
        <div class="book-card">
            <div class="book-image">
                <img src="/api/placeholder/400/600" alt="Book 6">
                <div class="book-overlay">
                    <div class="book-actions">
                        <a href="#"><span class="action-icon">❤</span></a>
                        <a href="#"><span class="action-icon">📑</span></a>
                        <a href="#"><span class="action-icon">ℹ</span></a>
                    </div>
                </div>
            </div>
            <div class="book-info">
                <h3>Beyond the Horizon</h3>
                <p>James Wilson</p>
                <div class="book-rating">
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star star-half"></span>
                </div>
                <div class="book-badge">Fantasy</div>
            </div>
        </div>
    </div>
    
    <h2 class="section-title">New Releases</h2>
    
    <div class="book-container">
        <div class="book-card">
            <div class="book-image">
                <img src="/api/placeholder/400/600" alt="New Book 1">
                <div class="book-overlay">
                    <div class="book-actions">
                        <a href="#"><span class="action-icon">❤</span></a>
                        <a href="#"><span class="action-icon">📑</span></a>
                        <a href="#"><span class="action-icon">ℹ</span></a>
                    </div>
                </div>
            </div>
            <div class="book-info">
                <h3>Whispers in the Dark</h3>
                <p>Robert Johnson</p>
                <div class="book-rating">
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star star-empty"></span>
                </div>
                <div class="book-badge">Thriller</div>
            </div>
        </div>
        
        <div class="book-card">
            <div class="book-image">
                <img src="/api/placeholder/400/600" alt="New Book 2">
                <div class="book-overlay">
                    <div class="book-actions">
                        <a href="#"><span class="action-icon">❤</span></a>
                        <a href="#"><span class="action-icon">📑</span></a>
                        <a href="#"><span class="action-icon">ℹ</span></a>
                    </div>
                </div>
            </div>
            <div class="book-info">
                <h3>Eternal Summer</h3>
                <p>Amanda Parker</p>
                <div class="book-rating">
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star star-half"></span>
                </div>
                <div class="book-badge">Romance</div>
            </div>
        </div>
        
        <div class="book-card">
            <div class="book-image">
                <img src="/api/placeholder/400/600" alt="New Book 3">
                <div class="book-overlay">
                    <div class="book-actions">
                        <a href="#"><span class="action-icon">❤</span></a>
                        <a href="#"><span class="action-icon">📑</span></a>
                        <a href="#"><span class="action-icon">ℹ</span></a>
                    </div>
                </div>
            </div>
            <div class="book-info">
                <h3>The Last Scientist</h3>
                <p>Thomas Reed</p>
                <div class="book-rating">
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star star-half"></span>
                    <span class="star star-empty"></span>
                </div>
                <div class="book-badge">Dystopian</div>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>About BookNest</h3>
                <p>BookNest is your digital haven for discovering, exploring, and enjoying books of all genres. Our mission is to connect readers with their next favorite story.</p>
                <div class="social-links">
                    <a href="#"><span class="social-icon">📘</span></a>
                    <a href="#"><span class="social-icon">📱</span></a>
                    <a href="#"><span class="social-icon">📸</span></a>
                    <a href="#"><span class="social-icon">📢</span></a>
                </div>