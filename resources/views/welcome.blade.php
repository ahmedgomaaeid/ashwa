<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ashwa Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <div>
        <div class="header-blue">
            <nav class="navbar navbar-dark navbar-expand-md navigation-clean-search">
                <div class="container"><a class="navbar-brand" href="#">Ashwa Store</a>

                </div>
            </nav>
            <div class="container hero">
                <div class="row">
                    <div class="col-12 col-lg-6 col-xl-5 offset-xl-1">
                        <h1>Ashwa Store App.</h1>
                        <p>
                            Ashwa Store is a platform that allows you to buy and sell products online. It is a platform that
                            connects buyers and sellers.
                        </p>
                         <button class="btn btn-light btn-lg action-button" type="button">Android</button>
                         <button class="btn btn-light btn-lg action-button" type="button">Apple</button>
                        </div>
                    <div
                        class="col-md-5 col-lg-5 offset-lg-1 offset-xl-0 d-none d-lg-block phone-holder">
                        <div class="iphone-mockup"><img src="{{asset('img/image.png')}}" class="device">

                        </div>
                </div>
            </div>
        </div>

    </div>
    <div class="products">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>Products</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card"><img
                            src="https://btech.com/media/catalog/product/7/4/74310b2e4538692fc98f6b7eca2f56ba91c5732b8003753b9cc99d6c836314e7.jpeg?width=500&store=en&image-type=image"
                            class="card-img-top">
                        <div class="card-body">
                            <h4 class="card-title
                                ">Iphone</h4>
                            <p class="card-text">The iPhone is a smartphone made by Apple that combines a computer, iPod, digital camera and cellular phone into one device with a touchscreen interface.</p>
                            <span class="price text-success">ريال7000</span>
                            <a href="purchase/1" class="btn btn-primary">Buy</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card"><img
                            src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR1s6NxAS1T4i1mtU71IOAEMCg68o1U_8VfDA&s"
                            class="card-img-top">
                        <div class="card-body">
                            <h4 class="card-title">Macbook</h4>
                            <p class="card-text">The MacBook is a brand of Macintosh laptop computers by Apple Inc. that merged the PowerBook and iBook lines during Apple's transition to Intel processors.</p>
                            <span class="price text-success">10000 ريال</span>
                            <a href="purchase/2" class="btn btn-primary">Buy</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card"><img
                            src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTK5j_25QRiz4-Au_1n-A-iVWm7JyBHL-kssQ&s"
                            class="card-img-top">
                        <div class="card-body">
                            <h4 class="card-title">Ipad</h4>
                            <p class="card-text">iPad is a line of tablet computers designed, developed and marketed by Apple Inc., which run the iOS and iPadOS mobile operating systems.</p>
                            <span class="price text-success">7000 ريال</span>
                            <a href="purchase/3" class="btn btn-primary">Buy</a>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <style>
        .products {
            padding: 50px 0;
        }
        .products h2 {
            text-align: center;
            margin-bottom: 50px;
        }
        .card {
            margin-bottom: 30px;
        }
        .card-title {
            text-align: center;
        }
        .card-text {
            text-align: center;
        }
        .card-body {
            text-align: center;
        }
        .card a {
            display: block;
            margin-top: 20px;
        }

    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
</body>

</html>
