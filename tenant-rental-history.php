<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karuhatan Public Market</title>
    <link href="assets/img/KPM Logo.png" rel="icon">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/tenant-rental-history-style.css">
    <link rel="stylesheet" href="bootstrap-5.3.2-dist/css/bootstrap.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg-tenant navbar-dark bg-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="tenant-dashboard.html">
          <img src="assets/img/Karuhatan Public Market Logo.png" alt="Karuhatan Public Market Logo" class="img-fluid" id="kpmLogo"/>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-between" id="navbarNavDropdown">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link nav-text" href="tenant-dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link nav-text dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">Rental&nbsp;</a>
                  <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                      <li>
                        <a class="dropdown-item nav-text" href="tenant-rental-payment.php">Payment</a>
                      </li>
                      <li>
                        <a class="dropdown-item nav-text" href="tenant-rental-history.php">History</a>
                      </li>
                  </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-text" href="tenant-applications.php">Application</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link nav-text" href="tenant-document.php">Document</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-text" href="tenant-guide-map.php">Guide Map</a>
                </li>
                <li class="nav-item" id="hidden-nav">
                  <a class="nav-link nav-text" href="tenant-notifications.php">Notifications</a>
                </li>
                <li class="nav-item dropdown" id="hidden-nav">
                  <a class="nav-link nav-text dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">juandelacruz@gmail.com&nbsp;</a>
                  <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                      <li>
                        <a class="dropdown-item nav-text" href="tenant-profile.php">Profile</a>
                      </li>
                      <li>
                        <a class="dropdown-item nav-text" href="home.html">Sign Out</a>
                      </li>
                  </ul>
                </li>
            </ul>
            <div class="d-flex align-items-center justify-content-center" id="hidden-side-nav">
                <div class="me-5 nav-text">
                    <a href="tenant-notifications.php">
                      <i class="fa-solid fa-bell" style="color: #283891"></i>
                    </a>
                </div>
                <div class="dropdown ms-6">
                    <button class="btn nav-text dropdown-toggle" style="border: none; padding-left: 5px" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">juandelacruz@gmail.com</button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item nav-text" href="tenant-profile.php">Profile</a></li>
                        <li><a class="dropdown-item nav-text" href="home.html">Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
  </nav>

    <div lc-helper="background" class="container-fluid py-0 d-flex justify-content-center" style="  background-image: url('assets/img/about-us-bg.png'); background-position: center; background-size:cover; background-repeat:no-repeat">
      <div class="container-fluid px-4 py-0 my-4 col-8 custom-text-center">
          <h2 class="display-3 switzer-bold-text-white">Rental History</h2>
      </div>
    </div>

    <div class="container py-7 px-0">
      <div class="py-5 px-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
          <div class="dropdown mb-1">
            <button class="btn btn-signup dropdown-toggle" type="button" id="statusFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                All
            </button>
            <ul class="dropdown-menu" aria-labelledby="statusFilterDropdown">
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" id="paidFilter">All</a></li>
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" id="unpaidFilter">Paid</a></li>
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" id="allFilter">Unpaid</a></li>
            </ul>
          </div>
          <table id="example" class="table" style="width:100%">
              <thead>
                  <tr class="display-sm">
                      <th>Status</th>
                      <th>Amount</th>
                      <th>Date</th>
                      <th>Payment Method</th>
                      <th>Actions</th>
                  </tr>
              </thead>
              <tbody>
                <tr class="paid">
                    <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                    <td>₱ 000.00</td>
                    <td>00/00/0000</td>
                    <td>GCash</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button">Details</button> 
                      </div>
                    </td>
                </tr>
                <tr class="unpaid">
                    <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                    <td>₱ 000.00</td>
                    <td>00/00/0000</td>
                    <td>----</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button">Pay</button> 
                      </div>
                    </td>
                </tr>
                <tr class="paid">
                    <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                    <td>₱ 000.00</td>
                    <td>00/00/0000</td>
                    <td>GCash</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button">Details</button> 
                      </div>
                    </td>
                </tr>
                <tr class="unpaid">
                    <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                    <td>₱ 000.00</td>
                    <td>00/00/0000</td>
                    <td>----</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button">Pay</button> 
                      </div>
                    </td>
                </tr>
                <tr class="paid">
                    <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                    <td>₱ 000.00</td>
                    <td>00/00/0000</td>
                    <td>GCash</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button">Details</button> 
                      </div>
                    </td>
                </tr>
                <tr class="unpaid">
                    <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                    <td>₱ 000.00</td>
                    <td>00/00/0000</td>
                    <td>----</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button">Pay</button> 
                      </div>
                    </td>
                </tr>
                <tr class="paid">
                    <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                    <td>₱ 000.00</td>
                    <td>00/00/0000</td>
                    <td>GCash</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button">Details</button> 
                      </div>
                    </td>
                </tr>
                <tr class="unpaid">
                  <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                  <td>₱ 000.00</td>
                  <td>00/00/0000</td>
                  <td>----</td>
                  <td>
                    <div>
                      <button class="btn btn-sm btn-signup btn-login px-4" type="button">Pay</button> 
                    </div>
                  </td>
              </tr>
              <tr class="paid">
                  <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                  <td>₱ 000.00</td>
                  <td>00/00/0000</td>
                  <td>GCash</td>
                  <td>
                    <div>
                      <button class="btn btn-sm btn-signup btn-login px-4" type="button">Details</button> 
                    </div>
                  </td>
              </tr>
              <tr class="unpaid">
                  <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                  <td>₱ 000.00</td>
                  <td>00/00/0000</td>
                  <td>----</td>
                  <td>
                    <div>
                      <button class="btn btn-sm btn-signup btn-login px-4" type="button">Pay</button> 
                    </div>
                  </td>
              </tr>
              <tr class="paid">
                  <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                  <td>₱ 000.00</td>
                  <td>00/00/0000</td>
                  <td>GCash</td>
                  <td>
                    <div>
                      <button class="btn btn-sm btn-signup btn-login px-4" type="button">Details</button> 
                    </div>
                  </td>
              </tr>
              <tr class="unpaid">
                  <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                  <td>₱ 000.00</td>
                  <td>00/00/0000</td>
                  <td>----</td>
                  <td>
                    <div>
                      <button class="btn btn-sm btn-signup btn-login px-4" type="button">Pay</button> 
                    </div>
                  </td>
              </tr>
              <tr class="paid">
                  <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                  <td>₱ 000.00</td>
                  <td>00/00/0000</td>
                  <td>GCash</td>
                  <td>
                    <div>
                      <button class="btn btn-sm btn-signup btn-login px-4" type="button">Details</button> 
                    </div>
                  </td>
              </tr>
            </tbody>
        </table>
        </div>
    </div>
    
    <footer class="text-center text-lg-start">
        <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
        </section>
      
        <section class="">
          <div class="container text-center text-md-start mt-5">
            <div class="row mt-3">
              <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                <a href="tenant-dashboard.php">
                    <img src="../Developer things/assets/img/Karuhatan Public Market Logo.png" alt="Karuhatan Public Market Logo" class="img-fluid mb-3 footer-logo">
                </a>
                <p class="switzer-medium-text">Join us at the Karuhatan Public Market, where every stall offers quality goods and every visit is an opportunity to discover something new.</p>
              </div>
      
              <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                <a href="tenant-rental-payment.php" style="text-decoration: none"><h6 class="footer-text mb-4">Payment</h6></a>
                <a href="tenant-applications.php" style="text-decoration: none"><h6 class="footer-text mb-4">Application</h6></a>
                <a href="tenant-document.php" style="text-decoration: none"><h6 class="footer-text mb-4">Document</h6></a>
                <a href="tenant-guide-map.php" style="text-decoration: none"><h6 class="footer-text mb-4">Guide Map</h6></a>

              </div>
      
              <div class="col-md-3 col-lg-2 col-xl-3 mx-auto mb-4">
                <a href="privacy-policy.html" style="text-decoration: none"><h6 class="footer-text mb-4">Privacy Policy</h6></a>
              </div>
      
              <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                <h6 class="footer-text mb-4">Contact</h6>
                <p class="switzer-medium-text"><i class="fas fa-home me-3 custom-icon-color"></i>#8 Karuhatan Road, Karuhatan Valenzuela City, 1441</p>
                <p class="switzer-medium-text"><i class="fas fa-envelope me-3 custom-icon-color"></i>Karuhatanmarketoffice@gmail.com</p>
                <p class="switzer-medium-text"><i class="fas fa-phone me-3 custom-icon-color"></i>09694252876</p>
              </div>
            </div>
          </div>
        </section>
      
        <div class="text-center switzer-semibold-text-white p-4" style="background-color: #283891;">© 2024 Copyright:
          <a class="switzer-semibold-text-white" href="#" style="text-decoration: none">Karuhatan Public Market</a>
        </div>
    </footer>

    <script src="bootstrap-5.3.2-dist/js/bootstrap.bundle.js "></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src='https://code.jquery.com/jquery-3.7.0.js'></script>
    <script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js'></script>
    <script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>

    <script>
      $(document).ready(function() {
        $(".statusFilter").click(function(){
            var selectedOption = $(this).text();
            $("#statusFilterDropdown").text(selectedOption);
        });
      });

      $(document).ready(function() {
        $(".statusFilter").click(function(event){
            event.preventDefault();
            var selectedOption = $(this).text();
            $("#statusFilterDropdown").text(selectedOption);
            filterRows(selectedOption);
        });

        function filterRows(option) {
            if (option === "All") {
                $("tbody tr").show();
            } else {
                $("tbody tr").hide();
                $("." + option.toLowerCase()).show();
            }
        }
    });

    $(document).ready(function() {
        var table = $('#example').DataTable({            
            "searching": false,
            
            "ordering": false
        });
    });
    </script>
</body>
</html>
