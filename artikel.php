<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

global $db;

$slug = $_GET['slug'] ?? '';

if (!$slug) {
    redirect('berita.php');
}

$article = getArticleBySlug($slug);

if (!$article) {
    redirect('berita.php');
}

// Get comments
$comments = getComments($article['id']);

// Get related articles
$related = $db->query("SELECT * FROM articles 
                      WHERE category_id = " . $article['category_id'] . " 
                      AND id != " . $article['id'] . " 
                      AND status = 'published' 
                      LIMIT 3")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $article['title']; ?> - SMP Muhammadiyah</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .article-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .article-header {
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
        }
        
        .article-date {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.6rem;
        }
        
        .article-header h1 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }
        
        .article-author {
            font-size: 1rem;
            color: #333;
            margin-bottom: 15px;
        }
        
        /* optional info container (currently commented out in markup) */
        .article-info {
            display: flex;
            gap: 20px;
            font-size: 14px;
            color: #666;
            flex-wrap: wrap;
        }
        
        .article-featured {
            width: 100%;
            height: 400px;
            background: #ddd;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .article-featured img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .article-content {
            font-size: 16px;
            line-height: 1.8;
            color: #333;
            margin-bottom: 40px;
            /* break long words and prevent overflow */
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-all;
            white-space: normal;
        }
        
        .article-content p {
            margin-bottom: 15px;
        }
        
        .comments-section {
            border-top: 2px solid #f0f0f0;
            padding-top: 40px;
            margin-top: 40px;
        }
        
        .comment {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-color);
        }
        
        .comment-author {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .comment-date {
            font-size: 12px;
            color: #999;
            margin-bottom: 10px;
        }
        
        .comment-form {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 5px;
            margin-top: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .btn-submit {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .related-articles {
            margin-top: 50px;
            padding-top: 40px;
            border-top: 2px solid #f0f0f0;
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav>
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">
                <span><img src="assets/images/logo.png" alt="Logo SMP Muhammadiyah"></span>
                <span>SMP Muhammadiyah<br>(Tahfidz) Salatiga</span>
            </a>
            <button class="nav-toggle">☰</button>
            <ul class="navbar-menu">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="akademik.php">Akademik</a></li>
                <li><a href="berita.php" class="active">Berita</a></li>
                <li><a href="spmb.php">SPMB</a></li>
                <li><a href="galeri.php">Galeri</a></li>
                <li><a href="kontak.php">Kontak</a></li>
            </ul>
        </div>
    </nav>

    <section class="section">
        <div class="article-container">
            <!-- ARTICLE HEADER -->
            <div class="article-header">
                <div class="article-date">📅 <?php echo formatDate($article['published_at']); ?></div>
                <h1><?php echo $article['title']; ?></h1>
                <div class="article-author">Oleh <?php echo $article['author_name']; ?></div>
                <!-- optional metadata could go here if needed -->
                <!-- <div class="article-info">
                    <span>📂 <?php echo $article['category_name']; ?></span>
                    <span>👁️ <?php echo $article['views']; ?> pembaca</span>
                </div> -->
            </div>
            
            <!-- FEATURED IMAGE -->
            <?php if ($article['featured_image']): ?>
                <div class="article-featured">
                    <img src="uploads/<?php echo $article['featured_image']; ?>" alt="<?php echo $article['title']; ?>">
                </div>
            <?php endif; ?>
            
            <!-- ARTICLE CONTENT -->
            <div class="article-content">
                <?php echo nl2br($article['content']); ?>
            </div>
            
            <!-- COMMENTS SECTION -->
            <div class="comments-section">
                <h2>Komentar (<?php echo count($comments); ?>)</h2>
                
                <?php if (count($comments) > 0): ?>
                    <div style="margin-bottom: 40px;">
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment">
                                <div class="comment-author"><?php echo $comment['author_name']; ?></div>
                                <div class="comment-date"><?php echo formatDate($comment['created_at']); ?></div>
                                <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                <?php if (!empty($comment['admin_reply'])): ?>
                                    <div class="comment-reply" style="margin-top:10px; padding:10px; background:#e5f3ff; border-left:4px solid var(--primary-color);">
                                        <strong>Balasan Admin:</strong>
                                        <p><?php echo nl2br(htmlspecialchars($comment['admin_reply'])); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #999; margin: 20px 0;">Belum ada komentar. Jadilah yang pertama!</p>
                <?php endif; ?>
                
                <!-- COMMENT FORM -->
                <div class="comment-form">
                    <h3>Tinggalkan Komentar</h3>
                    <form id="commentForm">
                        <div class="form-group">
                            <label for="name">Nama *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="comment">Komentar *</label>
                            <textarea id="comment" name="comment" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn-submit">Kirim Komentar</button>
                    </form>
                </div>
            </div>
            
            <!-- RELATED ARTICLES -->
            <?php if (count($related) > 0): ?>
                <div class="related-articles">
                    <h2>Artikel Terkait</h2>
                    <div class="cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); margin-top: 20px;">
                        <?php foreach ($related as $article_rel): ?>
                            <div class="card">
                                <div style="width: 100%; height: 150px; background: var(--primary-color); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    <?php echo $article_rel['category_name']; ?>
                                </div>
                                <div class="card-body">
                                    <h3 style="color: var(--primary-color); margin-bottom: 10px;"><?php echo $article_rel['title']; ?></h3>
                                    <p><?php echo substr(strip_tags($article_rel['content']), 0, 100); ?>...</p>
                                    <a href="artikel.php?slug=<?php echo $article_rel['slug']; ?>" class="btn btn-primary" style="margin-top: 10px;">Baca →</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                    <span style="font-size: 2.5rem;"><img src="assets/images/logo.png" alt="Logo SMP Muhammadiyah"></span>
                    <div style="font-weight: 700; font-size: 0.95rem;">SMP Muhammadiyah<br>(Tahfidz) Salatiga</div>
                </div>
                <p>Jl. Cempaka No.5-7, Jetis, Kecamatan Sidorejo, Kota Salatiga, Jawa Tengah 50711.</p>
                <p style="margin-top: 1.5rem; font-weight: 600;">Follow us on:</p>
                <div class="social-links">
                    <a href="https://facebook.com/smpmuhsltg" target="_blank" title="Facebook"><img src="assets/images/facebook.jpg" alt="facebook"></a>
                    <a href="https://instagram.com/smpmuhammadiyahsalatiga" target="_blank" title="Instagram"><img src="assets/images/instagram.jpg" alt="instagram"></a>
                    <a href="https://www.tiktok.com/@smpmuhammadiyahsalatiga" target="_blank" title="TikTok"><img src="assets/images/tiktok.jpg" alt="tiktok"></a>
                    <a href="https://youtube.com/@smpmuhammadiyahcempakasala5600" target="_blank" title="YouTube"><img src="assets/images/youtube.jpg" alt="youtube"></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <a href="index.php">Home</a>
                <a href="akademik.php">Akademik</a>
                <a href="berita.php">Berita</a>
                <a href="spmb.php">SPMB</a>
            </div>
            <div class="footer-section">
                <h4>All Pages</h4>
                <a href="akademik.php">Akademik</a>
                <a href="berita.php">Berita</a>
                <a href="kontak.php">Kontak</a>
                <a href="spmb.php">SPMB</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026, SMP Muhammadiyah Tahfidz. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
    <script>
        document.getElementById('commentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                article_id: <?php echo $article['id']; ?>,
                author_name: document.getElementById('name').value,
                author_email: document.getElementById('email').value,
                content: document.getElementById('comment').value
            };
            
            fetch('api/data.php?action=add_comment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Komentar Anda berhasil disubmit dan akan ditampilkan setelah disetujui');
                    document.getElementById('commentForm').reset();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
