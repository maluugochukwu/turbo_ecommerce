<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600&display=swap" rel="stylesheet">
</head>
<style>
    h2,h3,h4,h1,h5{
        font-family: 'Montserrat', sans-serif;
    }
    p{
        font-family: 'Georgia', sans-serif;
    }
    .footer li{
        list-style: none;
        color: #ccc;
        padding: 0;
    }
    .footer ul{
        margin: 0;
        padding: 0;
    }
</style>
<body>
    <div class="bg-dark">
        <div class="container">
            <h3 class="text-light py-3">My LOGO</h3>
        </div>
    </div>
    <div class="container">
    <nav class="navbar navbar-expand-lg navbar-light bg-transparent">
  <a class="navbar-brand" href="#">Home</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="#">About Us <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Contact Us</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
          News
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <div class="dropdown-item" style="height: 250px; width:730px" href="#">
              <div class="row">
                  <div class="col-sm-4 border" style="white-space: normal;">
                      <small>small text</small>
                      <h4>Title</h4>
                      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. </p>
                  </div>
                  <div class="col-sm-4" style="white-space: normal;">
                      <small>small text</small>
                      <h4>Title</h4>
                      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. </p>
                  </div>
                  <div class="col-sm-4" style="white-space: normal;">
                      <small>small text</small>
                      <h4>Title</h4>
                      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. </p>
                  </div>
              </div>
          </div>
          
        </div>
      </li>
      
    </ul>
    <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
      <button class="btn btn-outline-dark my-2 my-sm-0" type="submit">Search</button>
    </form>
  </div>
</nav>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-9">
                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                        <img class="d-block w-100" src="http://template.themeton.com/tana/images/news/slider-01.jpg" alt="First slide">
                        </div>
                        <div class="carousel-item">
                        <img class="d-block w-100" src="http://template.themeton.com/tana/images/news/slider-02.jpg" alt="Second slide">
                        </div>
                        <div class="carousel-item">
                        <img class="d-block w-100" src="http://template.themeton.com/tana/images/news/slider-03.jpg" alt="Third slide">
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
            <div class="col-sm-3">
                <div style="height:413px; overflow-x:scroll">
                    <div data-target="#carouselExampleIndicators" data-slide-to="0" class="active">
                        <img class="img-responsive" style="width:100%" src="http://template.themeton.com/tana/images/news/slider-01.jpg" alt="demo">
                    </div>
                    <div data-target="#carouselExampleIndicators" data-slide-to="1">
                        <img class="img-responsive" style="width:100%" src="http://template.themeton.com/tana/images/news/slider-02.jpg" alt="demo">
                    </div>
                    <div data-target="#carouselExampleIndicators" data-slide-to="2">
                        <img class="img-responsive" style="width:100%" src="http://template.themeton.com/tana/images/news/slider-03.jpg" alt="demo">
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <div class="container mt-5">
        <div>
            <div class="row">
                <div class="col-sm-10">
                    <div class="row no-gutters">
                        <div class="col-sm-6 ">
                            <img width="100%" src="http://template.themeton.com/tana/images/news/rightnow-01.jpg" alt="">
                            <small>Dave Clark 2h</small>
                            <h3>News Flash</h3>
                            <p>North Carolina Voter ID Law Is Upheld by Federal Judge...</p>
                        </div>
                        <div class="col-sm-6 pl-2">
                            <img width="100%" src="http://template.themeton.com/tana/images/news/rightnow-02.jpg" alt="">
                            <small>Dave Clark 2h</small>
                            <h3>News Flash</h3>
                            <p>North Carolina Voter ID Law Is Upheld by Federal Judge...</p>
                        </div>
                    </div>
                    <!-- <div style="display:grid; grid-template-columns: auto auto; column-gap: 5px">
                        <div >
                            <img width="100%" src="http://template.themeton.com/tana/images/news/rightnow-01.jpg" alt="">
                            <h3>News Flash</h3>
                        </div>
                        <div >
                            <img src="http://template.themeton.com/tana/images/news/rightnow-01.jpg" alt="">
                            <h3>News Flash</h3>
                        </div>
                        <div >
                            <img src="http://template.themeton.com/tana/images/news/rightnow-01.jpg" alt="">
                            <h3>News Flash</h3>
                        </div>
                    </div> -->
                </div>
                <div class="col-sm-2">
                    <h2 style="border-bottom: 1px solid #ccc;">Trending</h2>
                    <div>
                        <small>Dave Clark 2h</small>
                        <h3>News Flash</h3>
                        <p>North Carolina Voter ID Law Is Upheld by Federal Judge...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="g_id_onload"
         data-client_id="335006090314-6kk4mmbitkrmpn24mq5lhvbakb7fbkem.apps.googleusercontent.com"
         data-callback="onSignIn">
    </div>
    <div class="g_id_signin" data-type="popup"></div>
    <div class="footer text-light" style="background-color: #000; padding:70px 0px">
        <div class="container">
            <div class="row pb-3">
                <div class="col-sm-8">
                    <h2>My Logo</h2>
                </div>
                <div class="col-sm-4"></div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <h5>Video</h5>
                    <ul>
                        <li>Video News</li>
                        <li>TV Shows</li>
                    </ul>
                </div>
                <div class="col-sm-3"></div>
                <div class="col-sm-3"></div>
                <div class="col-sm-3"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
<script >
    
    function onSignIn(googleUser) {
        console.log(googleUser)
    }
</script>
</body>
</html>