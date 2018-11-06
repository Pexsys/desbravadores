<?php 
@require_once("../functions.php");
fHeaderPage();
?>
<body>

    <!-- Navigation -->
	<?php @include_once("../navbar.php");?>

    <!-- Page Content -->
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Sidebar Page
                    <small>Subheading</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php">Home</a>
                    </li>
                    <li class="active">Sidebar Page</li>
                </ol>
            </div>
        </div>
        <!-- /.row -->

        <!-- Content Row -->
        <div class="row">
            <!-- Sidebar Column -->
            <div class="col-md-3">
                <div class="list-group">
                    <a href="index.php" class="list-group-item">Home</a>
                    <a href="about.php" class="list-group-item">About</a>
                    <a href="services.php" class="list-group-item">Services</a>
                    <a href="contact.php" class="list-group-item">Contact</a>
                    <a href="portfolio-1-col.php" class="list-group-item">1 Column Portfolio</a>
                    <a href="portfolio-2-col.php" class="list-group-item">2 Column Portfolio</a>
                    <a href="portfolio-3-col.php" class="list-group-item">3 Column Portfolio</a>
                    <a href="portfolio-4-col.php" class="list-group-item">4 Column Portfolio</a>
                    <a href="portfolio-item.php" class="list-group-item">Single Portfolio Item</a>
                    <a href="blog-home-1.php" class="list-group-item">Blog Home 1</a>
                    <a href="blog-home-2.php" class="list-group-item">Blog Home 2</a>
                    <a href="blog-post.php" class="list-group-item">Blog Post</a>
                    <a href="full-width.php" class="list-group-item">Full Width Page</a>
                    <a href="sidebar.php" class="list-group-item active">Sidebar Page</a>
                    <a href="faq.php" class="list-group-item">FAQ</a>
                    <a href="404.php" class="list-group-item">404</a>
                    <a href="pricing.php" class="list-group-item">Pricing Table</a>
                </div>
            </div>
            <!-- Content Column -->
            <div class="col-md-9">
                <h2>Section Heading</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Soluta, et temporibus, facere perferendis veniam beatae non debitis, numquam blanditiis necessitatibus vel mollitia dolorum laudantium, voluptate dolores iure maxime ducimus fugit.</p>
            </div>
        </div>
        <!-- /.row -->

        <hr>

        <!-- Footer -->
		<?php @include_once("../footer.php");?>

    </div>
    <!-- /.container -->

<?php @include_once("../bottom_page.php");?>