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
                <h1 class="page-header">404
                    <small>Page Not Found</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php">Home</a>
                    </li>
                    <li class="active">404</li>
                </ol>
            </div>
        </div>
        <!-- /.row -->

        <div class="row">

            <div class="col-lg-12">
                <div class="jumbotron">
                    <h1><span class="error-404">404</span>
                    </h1>
                    <p>The page you're looking for could not be found. Here are some helpful links to get you back on track:</p>
                    <ul>
                        <li>
                            <a href="index.php">Home</a>
                        </li>
                        <li>
                            <a href="about.php">About</a>
                        </li>
                        <li>
                            <a href="services.php">Services</a>
                        </li>
                        <li>
                            <a href="contact.php">Contact</a>
                        </li>
                        <li>
                            Portfolio
                            <ul>
                                <li>
                                    <a href="portfolio-1-col.php">1 Column Portfolio</a>
                                </li>
                                <li>
                                    <a href="portfolio-2-col.php">2 Column Portfolio</a>
                                </li>
                                <li>
                                    <a href="portfolio-3-col.php">3 Column Portfolio</a>
                                </li>
                                <li>
                                    <a href="portfolio-4-col.php">4 Column Portfolio</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            Blog
                            <ul>
                                <li>
                                    <a href="blog-home-1.php">Blog Home 1</a>
                                </li>
                                <li>
                                    <a href="blog-home-2.php">Blog Home 2</a>
                                </li>
                                <li>
                                    <a href="blog-post.php">Blog Post</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            Other Pages
                            <ul>
                                <li>
                                    <a href="full-width-page.php">Full Width Page</a>
                                </li>
                                <li>
                                    <a href="sidebar.php">Sidebar Page</a>
                                </li>
                                <li>
                                    <a href="faq.php">FAQ</a>
                                </li>
                                <li>
                                    <a href="404.php">404 Page</a>
                                </li>
                                <li>
                                    <a href="pricing-table.php">Pricing Table</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        <hr>

        <!-- Footer -->
		<?php @include_once("../footer.php");?>

    </div>
    <!-- /.container -->

<?php @include_once("../bottom_page.php");?>