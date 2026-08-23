<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="icon" href="../assets/img/logo1.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,500;0,700;1,400&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:wght@400;700&family=Volkhov:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="dashboard/css/home.css">
</head>

<body class="body-whole">
    <img src="../assets/img/home-decor.png" class="bank-decor">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-transparent pb-5 animate__animated animate__fadeIn">
        <div class="container">
            <a class="navbar-brand nav-logo" href="#">
                <img src="../assets/img/home-logo.png" alt="Logo" width="200">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class=""><i class="fas fa-bars fa-sm"></i></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-white nav-change-color" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white nav-change-color" href="#about">About</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-white nav-change-color" href="../pages/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <div class="nav-signup-container">
                            <div class="nav-signup">
                                <a href="../pages/register.php" class="no-underline">Sign Up</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Home Front Section -->
    <section class="pt-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 animate__animated animate__fadeInLeft">
                    <p class="bank-subtitle mb-1">we don't just manage your money, we meme it.</p>
                    <h2 class="mb-3 bank-title">Putting "fun" <br> in "funds" <br> <Span class="title-main">since 1996</Span></h2>
                    <p class="bease-subtitle col-9 mb-4 bank-info">
                        We're not your stuffy, suit-and-tie bankers. We're the bank that gets you - the meme lords, the
                        avocado toast enthusiasts, the ones who still think dogecoin has potential.
                    </p>
                    <div class="row">
                        <div class="col-md-6 mb-5">
                            <a href="register.php"><button class="btn btn-primary border-0 bank-btn pulse">Register
                                    now</button></a>
                        </div>
                        <ul class="list-inline">
                            <li class="list-inline-item bank-phone"> <img src="../assets/img/home-call.png"
                                    height="20px" class="zoom-on-hover"> +01550690511</li>
                            <li class="list-inline-item"><span class="vertical-divider"></span></li>
                            <li class="list-inline-item zoom-on-hover fa-sm"><a href="#"><i class="fab fa-facebook fa-lg" id="icon-awesome"></i></a></li>
                            <li class="list-inline-item zoom-on-hover fa-sm"><a href="#"><i class="fab fa-instagram fa-lg" id="icon-awesome"></i></a></li>
                            <li class="list-inline-item zoom-on-hover fa-sm"><a href="#"><i class="fab fa-linkedin fa-lg" id="icon-awesome"></i></a></li>
                            <li class="list-inline-item"><a href="#"><i class="fab fa-youtube fa-lg"
                                        id="icon-awesome"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-7 d-none d-lg-block animate__animated animate__fadeInRight">
                    <div class="video-container">
                        <video autoplay loop muted class="bank-video" src="../assets/img/V.mp4"></video>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="gap"></div>

    <!-- Our Services -->

    <div class="gap"></div>

    <!-- We the Best -->
    <section id="about">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center best-subtitle animate__animated animate__fadeIn"><span class="header-1">The Broke But Woke Club</span></div>
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title animate__animated animate__fadeIn">We Offer the best</h2>
                </div>
            </div>
            <div class="row justify-content-center mt-3">
                <div class="col-lg-2 text-center mb-3 animate__animated animate__slideInUp" style="animation-delay: 0.1s;">
                    <img src="../assets/img/register-interest.png" height="99px" width="99px" alt="Interest Rates"
                        class="img-fluid mb-3">
                    <h3 class="best-title">Interest Rates</h3>
                    <p class="best-info">Built Wicket longer admire do barton vanity itself do in it.</p>
                </div>
                <div class="col-lg-2 text-center mb-3 animate__animated animate__slideInUp" style="animation-delay: 0.2s;">
                    <img src="../assets/img/register-saving.png" height="99px" width="99px" alt="Savings"
                        class="img-fluid mb-3">
                    <h3 class="best-title">Savings</h3>
                    <p class="best-info">Engrossed listening. Park gate sell they west hard for the.</p>
                </div>
                <div class="col-lg-2 text-center mb-3 animate__animated animate__slideInUp" style="animation-delay: 0.3s;">
                    <img src="../assets/img/register-digital.png" height="95px" width="95px" alt="Digital Services"
                        class="img-fluid mb-3">
                    <h3 class="best-title">Digital Services</h3>
                    <p class="best-info">Barton vanity itself do in it. Preferd to men it engrossed listening. </p>
                </div>
                <div class="col-lg-2 text-center mb-3 animate__animated animate__slideInUp" style="animation-delay: 0.4s;">
                    <img src="../assets/img/register-atm.png" height="99px" width="99px" alt="Safe Deposit Locker"
                        class="img-fluid mb-3">
                    <h3 class="best-title">Safe Deposit</h3>
                    <p class="best-info">We deliver outsourced aviation services for military customers</p>
                </div>
                <div class="col-lg-2 text-center mb-3 animate__animated animate__slideInUp" style="animation-delay: 0.5s;">
                    <img src="../assets/img/register-card.png" height="101px" width="101px" alt="Service 5"
                        class="img-fluid mb-3">
                    <h3 class="best-title">Credit/Debit Card</h3>
                    <p class="best-info">We deliver outsourced aviation services for military customers</p>
                </div>
            </div>
        </div>
    </section>
    <div class="gap"></div>
    <section id="services">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-3">
                    <h2 class="font-weight-bold section-title animate__animated animate__fadeInDown">Our Services</h2>
                </div>
            </div>
            <div class="row justify-content-center mb-5">
                <div class="col-12 col-md-8 text-center">
                    <p class="text-center section-subtitle animate__animated animate__fadeInUp">
                        Tired of banking apps that look like they were designed by a hamster on acid? Ditch the snooze
                        and join the financial fiesta at Sawongam Bank! Where your hard-earned moolah gets more action
                        than a Dogecoin on Elon's Twitter feed.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <!-- Budget Beasts -->
    <section id="beast">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 animate__animated animate__fadeInLeft">
                    <h2 class="beast-title mb-3">Budget Beasts, Not <br> Budget Bums</h2>
                    <p class="bease-subtitle mb-5">Unleash Your Inner Financial Ninja with Sawongam Bank's Killer Tools
                    </p>
                    <div class="row">
                        <div class="col-md-4 mb-5 text-center animate__animated animate__fadeIn" style="animation-delay: 0.2s;">
                            <img src="../assets/img/budget-coin.png" height="58px" width="62px" alt="Interest Rate"
                                class="img-fluid">
                            <h3 class="image-title beast-info mt-3">Your money grooves with our amazing interest rates.
                            </h3>
                        </div>
                        <div class="col-md-4 mb-5 text-center animate__animated animate__fadeIn" style="animation-delay: 0.4s;">
                            <img src="../assets/img/budget-saving.png" height="50px" width="58px" alt="Savings"
                                class="img-fluid">
                            <h3 class="image-title beast-info mt-3">We make finance so lively, your savings might break
                                into a jig.</h3>
                        </div>
                        <div class="col-md-4 mb-5 text-center animate__animated animate__fadeIn" style="animation-delay: 0.6s;">
                            <img src="../assets/img/budget-security.png" height="50px" width="55px" alt="Security"
                                class="img-fluid">
                            <h3 class="image-title beast-info mt-3">Our security is tighter than your grandma's salsa
                                moves.</h3>
                        </div>
                        <ul class="list-inline animate__animated animate__fadeIn" style="animation-delay: 0.8s;">
                            <li class="list-inline-item"><a href="#"><i class="fab fa-facebook fa-lg"
                                        id="icon-awesome"></i></a></li>
                            <li class="list-inline-item"><a href="#"><i class="fab fa-instagram fa-lg"
                                        id="icon-awesome"></i></a></li>
                            <li class="list-inline-item"><a href="#"><i class="fab fa-linkedin fa-lg"
                                        id="icon-awesome"></i></a></li>
                            <li class="list-inline-item"><a href="#"><i class="fab fa-youtube fa-lg"
                                        id="icon-awesome"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block animate__animated animate__fadeInRight">
                    <img src="../assets/img/budget-block.png" alt="Image 4" class="img-fluid">
                </div>
            </div>
        </div>
    </section>
    <div class="gap"></div>




    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script>
        // Initialize WOW.js for scroll animations
        new WOW().init();

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();

                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>

</html>