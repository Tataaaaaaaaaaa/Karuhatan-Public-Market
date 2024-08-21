<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
if ($conn->connect_error) {
  echo json_encode(array("success" => false, "message" => "Connection failed: " . $conn->connect_error));
} else {
  $stmt_initial_load = $conn->prepare("SELECT occupiedStall, stallCategory FROM user_registration");
  $stmt_initial_load->execute();
  $result_initial_load = $stmt_initial_load->get_result();

  $stalls = array();
  while ($row = $result_initial_load->fetch_assoc()) {
      $stalls[] = $row;
  }

  $conn->close();
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karuhatan Public Market</title>
    <link href="assets/img/KPM Logo.png" rel="icon">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />
    <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/guide-map-style.css">
    <link rel="stylesheet" href="bootstrap-5.3.2-dist/css/bootstrap.css">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-navbar">
      <div class="container-fluid">
          <a class="navbar-brand" href="home.html">
              <img src="assets/img/Karuhatan Public Market Logo.png" alt="Karuhatan Public Market Logo" class="img-fluid" id="kpmLogo"/>
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-between" id="navbarNavDropdown">
              <ul class="navbar-nav mx-auto">
                  <li class="nav-item">
                      <a class="nav-link nav-text" href="home.html">Home</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link nav-text" href="guide-map.php">Guide Map</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link nav-text" href="updates.html">Updates</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link nav-text" href="about-us.html">About Us</a>
                  </li>
                  <li class="nav-item" id="hidden-nav">
                      <a class="nav-link nav-text" href="log-in.html">Login</a>
                  </li>
                  <li class="nav-item" id="hidden-nav">
                      <a class="nav-link nav-text" href="sign-up.html">Sign Up</a>
                  </li>
              </ul>
              <div class="d-flex" id="hidden-side-nav">
                  <a href="log-in.html">
                      <button class="btn nav-text ms-6">Login</button>
                  </a>
                  <a href="sign-up.html">
                      <button class="btn btn-signup ms-3">Sign Up</button>
                  </a>
              </div>
          </div>
      </div>
    </nav>
		
		<div class="d-flex align-items-stretch">
			<nav id="sidebar">
				<div class="custom-menu">
					<button type="button" id="sidebarCollapse" class="btn-1 btn-primary-1">
	          <i class="fa fa-bars"></i>
	          <span class="sr-only">Toggle Menu</span>
	        </button>
        </div>
        <div class="container-fluid py-5 d-flex justify-content-center" style="  background-image: url('assets/img/about-us-bg.png'); background-position: center; background-size:cover; background-repeat:no-repeat">
          <h1 class="text-center switzer-extrabold-text-white" style="font-size: 45px;">Guide Map</h1>
        </div>
				<div>
	        <ul class="list-unstyled components">
            <li class="active">
              <a href="#stallCategoryMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle switzer-semibold-text" style="font-size: 20px; text-decoration: none"><i class="fa-solid fa-store"></i>&nbsp;&nbsp;Stall Category</a>
              
              <div class="sublist">
                <ul class="collapse list-unstyled" id="stallCategoryMenu">
                  <li class="subitem-border" id="show-hide-canteen">
                      <a href="#" class="switzer-semibold-text" style="font-size: 20px; text-decoration: none"><i class="fa-solid fa-utensils"></i>&nbsp;&nbsp;&nbsp;Canteen<i id="show-hide-canteen-icon" class="fa-regular fa-eye" style="float:right;"></i></a>
                  </li>
                  <li class="subitem-border" id="show-hide-clothing">
                      <a href="#" class="switzer-semibold-text" style="font-size: 20px; text-decoration: none"><i class="fa-solid fa-shirt"></i>&nbsp;&nbsp;&nbsp;Clothing<i id="show-hide-clothing-icon" class="fa-regular fa-eye" style="float:right;"></i></a>
                  </li>
                  <li class="subitem-border" id="show-hide-coconut">
                      <a href="#" class="switzer-semibold-text" style="font-size: 20px; text-decoration: none"><i class="fa-regular fa-circle"></i>&nbsp;&nbsp;&nbsp;Coconut<i id="show-hide-coconut-icon" class="fa-regular fa-eye" style="float:right;"></i></a>
                  </li>
                  <li class="subitem-border" id="show-hide-condiments">
                      <a href="#" class="switzer-semibold-text" style="font-size: 20px; text-decoration: none"><i class="fa-solid fa-bottle-droplet"></i>&nbsp;&nbsp;&nbsp;Condiments<i id="show-hide-condiments-icon" class="fa-regular fa-eye" style="float:right;"></i></a>
                  </li>
                  <li class="subitem-border" id="show-hide-fruit">
                      <a href="#" class="switzer-semibold-text" style="font-size: 20px; text-decoration: none"><i class="fa-solid fa-apple-whole"></i>&nbsp;&nbsp;&nbsp;Fruit<i id="show-hide-fruit-icon" class="fa-regular fa-eye" style="float:right;"></i></a>
                  </li>
                  <li class="subitem-border" id="show-hide-grocery">
                      <a href="#" class="switzer-semibold-text" style="font-size: 20px; text-decoration: none"><i class="fa-solid fa-cart-shopping"></i>&nbsp;&nbsp;&nbsp;Grocery<i id="show-hide-grocery-icon" class="fa-regular fa-eye" style="float:right;"></i></a>
                  </li>
                  <li class="subitem-border" id="show-hide-meat">
                      <a href="#" class="switzer-semibold-text" style="font-size: 20px; text-decoration: none"><i class="fa-solid fa-cow"></i>&nbsp;&nbsp;&nbsp;Meat<i id="show-hide-meat-icon" class="fa-regular fa-eye" style="float:right;"></i></a>
                  </li>
                  <li class="subitem-border" id="show-hide-pharmacy">
                      <a href="#" class="switzer-semibold-text" style="font-size: 20px; text-decoration: none"><i class="fa-solid fa-prescription-bottle-medical"></i>&nbsp;&nbsp;&nbsp;Pharmacy<i id="show-hide-pharmacy-icon" class="fa-regular fa-eye" style="float:right;"></i></a>
                  </li>
                  <li class="subitem-border" id="show-hide-poultry">
                      <a href="#" class="switzer-semibold-text" style="font-size: 20px; text-decoration: none"><i class="fa-solid fa-egg"></i>&nbsp;&nbsp;&nbsp;&nbsp;Poultry<i id="show-hide-poultry-icon" class="fa-regular fa-eye" style="float:right;"></i></a>
                  </li>
                  <li class="subitem-border" id="show-hide-rice">
                      <a href="#" class="switzer-semibold-text" style="font-size: 20px; text-decoration: none"><i class="fa-solid fa-bowl-rice"></i>&nbsp;&nbsp;&nbsp;Rice<i id="show-hide-rice-icon" class="fa-regular fa-eye" style="float:right;"></i></a>
                  </li>
                  <li class="subitem-border" id="show-hide-sarisari">
                      <a href="#" class="switzer-semibold-text" style="font-size: 20px; text-decoration: none"><i class="fa-solid fa-basket-shopping"></i>&nbsp;&nbsp;&nbsp;SariSari<i id="show-hide-sarisari-icon" class="fa-regular fa-eye" style="float:right;"></i></a>
                  </li>
                  <li class="subitem-border" id="show-hide-toys">
                      <a href="#" class="switzer-semibold-text" style="font-size: 20px; text-decoration: none"><i class="fa-solid fa-dice"></i>&nbsp;&nbsp;&nbsp;Toys<i id="show-hide-toys-icon" class="fa-regular fa-eye" style="float:right;"></i></a>
                  </li>
                  <li class="subitem-border" id="show-hide-vegetable">
                      <a href="#" class="switzer-semibold-text" style="font-size: 20px; text-decoration: none"><i class="fa-solid fa-leaf"></i>&nbsp;&nbsp;&nbsp;Vegetable<i id="show-hide-vegetable-icon" class="fa-regular fa-eye" style="float:right;"></i></a>
                  </li>
                </ul>
              </div>
            </li>
	        </ul>

	      </div>
    	</nav>

      <div id="map" style="visibility: hidden;"></div>

	</div>

  <script>
    var occupied = {
      color: '#32cd32',
      fillColor: '#a0e8a0',
      fillOpacity: 1
    };

    var vacant = {
      color: '#acacac',
      fillColor: '#d3d3d3',
      fillOpacity: 1
    };

    var stair = {
      color: '#ff859a',
      fillColor: '#ffc0cb',
      fillOpacity: 1
    };

    var freezer = {
      color: '#4e4eff',
      fillColor: '#b1b1ff',
      fillOpacity: 1
    };

    var solidWhiteLineBeigeFill = {
      color: 'white',
      fillColor: '#e4ddd0',
      fillOpacity: 1
    };

    var solidGrayLine = {
      color: '#b5b5b5',
      fillColor: 'white',
      fillOpacity: 0
    };

    var solidBlackLine = {
      color: 'black',
      fillColor: 'white',
      fillOpacity: 0
    };

    var solidWhiteFill = {
      color: 'white',
      fillColor: 'white',
      fillOpacity: 1
    };

    var phaseOneFill = {
      color: 'none',
      fillColor: '#fd8d5c',
      fillOpacity: 1
    };

    var phaseTwoFill = {
      color: 'none',
      fillColor: '#17b5a0',
      fillOpacity: 1
    };

    var phaseThreeFill = {
      color: 'none',
      fillColor: '#fdb117',
      fillOpacity: 1
    };

    var phaseFourFill = {
      color: 'none',
      fillColor: '#8633a9',
      fillOpacity: 1
    };

    var arrowFill = {
      color: 'none',
      fillColor: 'none',
    };

    var legendFill = {
      color: '#b5b5b5',
      weight: 4,
      fillColor: 'white  ',
      fillOpacity: 1
    };

    var containerWallPolyLine1 = L.polyline([
      [0, 25],
      [0, 0],
      [290.25, 0],
      [290.25, 225],
      [515.875, 225],
      [515.875, 375],
      [0, 375],
      [0, 350],
      [0, 325],
    ], {
      color: '#b5b5b5',
      weight: 6
    });

    var containerWallPolyLine2 = L.polyline([
      [0, 350],
      [0, 325],
    ], {
      color: 'white',
      weight: 6
    });

    var containerWallPolyLine3 = L.polyline([
      [0, 324.8],
      [0, 275],
    ], {
      color: '#b5b5b5',
      weight: 6
    });

    var containerWallPolyLine4 = L.polyline([
      [0, 275],
      [0, 250],
    ], {
      color: 'white',
      weight: 6
    });

    var containerWallPolyLine5 = L.polyline([
      [0, 249.8],
      [0, 200],
    ], {
      color: '#b5b5b5',
      weight: 6
    });

    var containerWallPolyLine6 = L.polyline([
      [0, 200],
      [0, 175],
    ], {
      color: 'white',
      weight: 6
    });

    var containerWallPolyLine7 = L.polyline([
      [0, 174.8],
      [0, 125],
    ], {
      color: '#b5b5b5',
      weight: 6
    });

    var containerWallPolyLine8 = L.polyline([
      [0, 125],
      [0, 100],
    ], {
      color: 'white',
      weight: 6
    });

    var containerWallPolyLine9 = L.polyline([
      [0, 99.8],
      [0, 50],
    ], {
      color: '#b5b5b5',
      weight: 6
    });

    var containerWallPolyLine10 = L.polyline([
      [0, 50],
      [0, 25],
    ], {
      color: 'white',
      weight: 6
    })

    var containerWall = L.layerGroup([containerWallPolyLine1, containerWallPolyLine3, containerWallPolyLine5, containerWallPolyLine7, containerWallPolyLine9, containerWallPolyLine10 , containerWallPolyLine2, containerWallPolyLine4, containerWallPolyLine6, containerWallPolyLine8]);

    var pathPhaseOne = L.polygon([
      [0, 0],
      [290.25, 0],
      [290.25, 75],
      [0, 75],
      [0, 0],
    ],phaseOneFill);

    var pathPhaseTwo = L.polygon([
      [0, 75],
      [290.25, 75],
      [290.25, 225],
      [0, 225],
      [0, 75],
    ],phaseTwoFill);

    var pathPhaseThree = L.polygon([
      [0, 225],
      [515.875, 225],
      [515.875, 300],
      [0, 300],
      [0, 225],
    ],phaseThreeFill);

    var pathPhaseFour = L.polygon([
      [0, 300],
      [515.875, 300],
      [515.875, 375],
      [0, 375],
      [0, 300],
    ],phaseFourFill);

    var marketPhaseLabel = L.polygon([
      [500, -240],
      [300, -240],
      [300, -150],
      [500, -150],
    ], arrowFill).bindTooltip('<div style="text-align:left"><p class="title-label market-title">MARKET</p><p class="title-label market-title">PHASES:</p><br><p class="phase-label phase-legend m-0 p-0"><i class="fa-solid fa-square m-0 p-0" style="color: #fe560d"></i>&nbsp;&nbsp;Phase 1</p><p class="phase-label phase-legend m-0 p-0"><i class="fa-solid fa-square m-0 p-0" style="color: #00af98"></i>&nbsp;&nbsp;Phase 2</p><p class="phase-label phase-legend m-0 p-0"><i class="fa-solid fa-square m-0 p-0" style="color: #ffab00"></i>&nbsp;&nbsp;Phase 3</p><p class="phase-label phase-legend m-0 p-0"><i class="fa-solid fa-square m-0 p-0" style="color: #7b1fa2"></i>&nbsp;&nbsp;Phase 4</p></div>', {
      permanent: true,
      direction: 'center',
      className: 'market-title-tooltip'
    });

    var legendLabel = L.polygon([
      [200, -270],
      [-100, -270],
      [-100, -150],
      [200, -150],
    ], arrowFill).bindTooltip('<div style="text-align:left"><p class="legend-title-label market-title">Legend:</p><br><p class="phase-label phase-legend m-0 p-0"><i class="fa-solid fa-square m-0 p-0" style="color: #a0e8a0; background-color: #32cd32; border-bottom: 2px solid #32cd32; border-top: 2px solid #32cd32; border-left: 3px solid #32cd32; border-right: 4px solid #32cd32; border-radius: 4px"></i>&nbsp;&nbsp;Occupied Stall</p><p class="phase-label phase-legend m-0 p-0"><i class="fa-solid fa-square m-0 p-0" style="color: #d3d3d3; background-color: #acacac; border-bottom: 2px solid #acacac; border-top: 2px solid #acacac; border-left: 3px solid #acacac; border-right: 4px solid #acacac; border-radius: 4px"></i>&nbsp;&nbsp;Vacant Stall</p><p class="phase-label phase-legend m-0 p-0"><i class="fa-solid fa-square m-0 p-0" style="color: #ffc0cb; background-color: #ff859a; border-bottom: 2px solid #ff859a; border-top: 2px solid #ff859a; border-left: 3px solid #ff859a; border-right: 4px solid #ff859a; border-radius: 4px"></i>&nbsp;&nbsp;Stairs</p><p class="phase-label phase-legend m-0 p-0"><i class="fa-solid fa-square m-0 p-0" style="color: #b1b1ff; background-color: #4e4eff; border-bottom: 2px solid #4e4eff; border-top: 2px solid #4e4eff; border-left: 3px solid #4e4eff; border-right: 4px solid #4e4eff; border-radius: 4px"></i>&nbsp;&nbsp;Freezer</p></div>', {
      permanent: true,
      direction: 'center',
      className: 'market-title-tooltip'
    });

    var entrancePhaseOne = L.polygon([
      [-45, 0],
      [0, 0],
      [0, 75],
      [-45, 75],
      [-45, 0],
    ], arrowFill).bindTooltip('<div class="entrance-arrow-container"><i class="fa-solid fa-arrows-up-to-line entrance-arrow"></i><div class="entry-text">ENTRY</div></div>', {
      permanent: true,
      direction: 'center',
      className: 'entrance-arrow-tooltip'
    });

    var entrancePhaseTwo = L.polygon([
      [-45, 100],
      [0, 100],
      [0, 125],
      [-45, 125],
      [-45, 100],
    ], arrowFill).bindTooltip('<div class="entrance-arrow-container"><i class="fa-solid fa-arrows-up-to-line entrance-arrow"></i><div class="entry-text">ENTRY</div></div>', {
      permanent: true,
      direction: 'center',
      className: 'entrance-arrow-tooltip'
    });

    var entrancePhaseTwo_2 = L.polygon([
      [-45, 175],
      [0, 175],
      [0, 200],
      [-45, 200],
      [-45, 175],
    ], arrowFill).bindTooltip('<div class="entrance-arrow-container"><i class="fa-solid fa-arrows-up-to-line entrance-arrow"></i><div class="entry-text">ENTRY</div></div>', {
      permanent: true,
      direction: 'center',
      className: 'entrance-arrow-tooltip'
    });

    var entrancePhaseThree = L.polygon([
      [-45, 250],
      [0, 250],
      [0, 275],
      [-45, 275],
      [-45, 250],
    ], arrowFill).bindTooltip('<div class="entrance-arrow-container"><i class="fa-solid fa-arrows-up-to-line entrance-arrow"></i><div class="entry-text">ENTRY</div></div>', {
      permanent: true,
      direction: 'center',
      className: 'entrance-arrow-tooltip'
    });

    var entrancePhaseFour = L.polygon([
      [-45, 325],
      [0, 325],
      [0, 350],
      [-45, 350],
      [-45, 325],
    ], arrowFill).bindTooltip('<div class="entrance-arrow-container"><i class="fa-solid fa-arrows-up-to-line entrance-arrow"></i><div class="entry-text">ENTRY</div></div>', {
      permanent: true,
      direction: 'center',
      className: 'entrance-arrow-tooltip'
    });

    var legendPhaseOne = L.polygon([
      [400, -150],
      [300, -150],
      [300, 150],
      [400, 150],
    ], legendFill);

    var icons = {
      "Canteen": '<i class="fa-solid fa-utensils stall-label m-0 p-0"></i>',
      "Clothing": '<i class="fa-solid fa-shirt stall-label m-0 p-0"></i>',
      "Coconut": '<i class="fa-regular fa-circle stall-label m-0 p-0"></i>',
      "Condiments": '<i class="fa-solid fa-bottle-droplet stall-label m-0 p-0"></i>',
      "Fruits": '<i class="fa-solid fa-apple-whole stall-label m-0 p-0"></i>',
      "Grocery": '<i class="fa-solid fa-cart-shopping stall-label m-0 p-0"></i>',
      "Meat": '<i class="fa-solid fa-cow stall-label m-0 p-0"></i>',
      "Pharmacy": '<i class="fa-solid fa-prescription-bottle-medical stall-label m-0 p-0"></i>',
      "Poultry": '<i class="fa-solid fa-egg stall-label m-0 p-0"></i>',
      "Rice": '<i class="fa-solid fa-bowl-rice stall-label m-0 p-0"></i>',
      "SariSari": '<i class="fa-solid fa-basket-shopping stall-label m-0 p-0"></i>',
      "Toys": '<i class="fa-solid fa-dice stall-label m-0 p-0"></i>',
      "Vegetables": '<i class="fa-solid fa-leaf stall-label m-0 p-0"></i>'
    };

    var polygons = {
        "P1-1": L.polygon([[0, 0], [23.5, 0], [23.5, 25], [0, 25], [0, 0]], vacant).bindTooltip('', { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P1-2": L.polygon([[23.5, 0], [47, 0], [47, 25], [23.5, 25], [23.5, 0]], vacant).bindTooltip('', { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P1-3": L.polygon([[47, 0], [70.5, 0], [70.5, 25], [47, 25], [47, 0]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P1-4": L.polygon([[98.125, 0], [146.15625, 0], [146.15625, 25], [98.125, 25], [98.125, 0]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P1-5": L.polygon([[146.15625, 0], [194.1875, 0], [194.1875, 25], [146.15625, 25], [146.15625, 0]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P1-6": L.polygon([[194.1875, 0], [242.21875, 0], [242.21875, 25], [194.1875, 25], [194.1875, 0]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P1-7": L.polygon([[242.21875, 0], [290.25, 0], [290.25, 25], [242.21875, 25], [242.21875, 0]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P1-8": L.polygon([[0, 50], [17.625, 50], [17.625, 75], [0, 75], [0, 50]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P1-9": L.polygon([[17.625, 50], [35.25, 50], [35.25, 75], [17.625, 75], [17.625, 50]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P1-10": L.polygon([[35.25, 50], [52.875, 50], [52.875, 75], [35.25, 75], [35.25, 50]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P1-11": L.polygon([[52.875, 50], [70.5, 50], [70.5, 75], [52.875, 75], [52.875, 50]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P1-12": L.polygon([[98.125, 50], [146.15625, 50], [146.15625, 75], [98.125, 75], [98.125, 50]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P1-13": L.polygon([[146.15625, 50], [194.1875, 50], [194.1875, 75], [146.15625, 75], [146.15625, 50]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P1-14": L.polygon([[194.1875, 50], [242.21875, 50], [242.21875, 75], [194.1875, 75], [194.1875, 50]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P1-15": L.polygon([[242.21875, 50], [290.25, 50], [290.25, 75], [242.21875, 75], [242.21875, 50]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),

        "P2-1": L.polygon([[0, 75], [23.5, 75], [23.5, 100], [0, 100], [0, 75]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-2": L.polygon([[23.5, 75], [47, 75], [47, 100], [23.5, 100], [23.5, 75]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-3": L.polygon([[47, 75],[70.5, 75],[70.5, 100],[47, 100],[47, 75]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-4": L.polygon([[98.125, 75], [115.75, 75], [115.75, 100], [98.125, 100], [98.125, 75]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-5": L.polygon([[115.75, 75], [133.375, 75], [133.375, 100], [115.75, 100], [115.75, 75]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-6": L.polygon([[133.375, 75], [151, 75], [151, 100], [133.375, 100], [133.375, 75]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-7": L.polygon([[151, 75], [168.625, 75], [168.625, 100], [151, 100], [151, 75]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-8": L.polygon([[168.625, 75], [186.25, 75], [186.25, 100], [168.625, 100], [168.625, 75]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-9": L.polygon([[267.625, 75], [290.25, 75], [290.25, 110], [267.625, 110], [267.625, 75]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-10": L.polygon([[0, 125], [17.625, 125], [17.625, 150], [0, 150], [0, 125]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-11": L.polygon([[17.625, 125], [35.25, 125], [35.25, 150], [17.625, 150], [17.625, 125]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-12": L.polygon([[35.25, 125], [52.875, 125], [52.875, 150], [35.25, 150], [35.25, 125]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-13": L.polygon([[52.875, 125], [70.5, 125], [70.5, 150], [52.875, 150], [52.875, 125]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-14": L.polygon([[80.5, 125], [98.125, 125], [98.125, 150], [80.5, 150], [80.5, 125]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-15": L.polygon([[98.125, 125], [115.75, 125], [115.75, 150], [98.125, 150], [98.125, 125]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-16": L.polygon([[115.75, 125], [133.375, 125], [133.375, 150], [115.75, 150], [115.75, 125]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-17": L.polygon([[133.375, 125], [151, 125], [151, 150], [133.375, 150], [133.375, 125]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-18": L.polygon([[151, 125], [168.625, 125], [168.625, 150], [151, 150], [151, 125]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-19": L.polygon([[196.25, 125], [243.25, 125], [243.25, 150], [196.25, 150], [196.25, 125]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-20": L.polygon([[17.625, 150], [35.25, 150], [35.25, 175], [17.625, 175], [17.625, 150]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-21": L.polygon([[35.25, 150], [70.5, 150], [70.5, 175], [35.25, 175], [35.25, 150]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-22": L.polygon([[80.5, 150], [98.125, 150], [98.125, 175], [80.5, 175], [80.5, 150]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-23": L.polygon([[98.125, 150], [115.75, 150], [115.75, 175], [98.125, 175], [98.125, 150]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-24": L.polygon([[115.75, 150], [133.375, 150], [133.375, 175], [115.75, 175], [115.75, 150]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-25": L.polygon([[133.375, 150], [151, 150], [151, 175], [133.375, 175], [133.375, 150]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-26": L.polygon([[151, 150], [168.625, 150], [168.625, 175], [151, 175], [151, 150]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-27": L.polygon([[196.25, 150], [243.25, 150], [243.25, 175], [196.25, 175], [196.25, 150]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-28": L.polygon([[243.25, 150], [290.25, 150], [290.25, 175], [243.25, 175], [243.25, 150]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-29": L.polygon([[0, 200], [17.625, 200], [17.625, 225], [0, 225], [0, 200]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-30": L.polygon([[17.625, 200], [35.25, 200], [35.25, 225], [17.625, 225], [17.625, 200]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-31": L.polygon([[35.25, 200], [52.875, 200], [52.875, 225], [35.25, 225], [35.25, 200]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-32": L.polygon([[52.875, 200], [70.5, 200], [70.5, 225], [52.875, 225], [52.875, 200]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-33": L.polygon([[80.5, 200], [98.125, 200], [98.125, 225], [80.5, 225], [80.5, 200]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-34": L.polygon([[98.125, 200], [115.75, 200], [115.75, 225], [98.125, 225], [98.125, 200]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-35": L.polygon([[115.75, 200], [133.375, 200], [133.375, 225], [115.75, 225], [115.75, 200]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-36": L.polygon([[133.375, 200], [151, 200], [151, 225], [133.375, 225], [133.375, 200]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-37": L.polygon([[151, 200], [168.625, 200], [168.625, 225], [151, 225], [151, 200]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-38": L.polygon([[196.25, 187.5], [243.25, 187.5], [243.25, 212.5], [196.25, 212.5], [196.25, 187.5]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P2-39": L.polygon([[243.25, 187.5], [290.25, 187.5], [290.25, 212.5], [243.25, 212.5], [243.25, 187.5]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),

        "P3-1": L.polygon([[0, 225], [23.5, 225], [23.5, 250], [0, 250], [0, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-2": L.polygon([[23.5, 225], [47, 225], [47, 250], [23.5, 250], [23.5, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-3": L.polygon([[47, 225], [70.5, 225], [70.5, 250], [47, 250], [47, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-4": L.polygon([[80.5, 225], [95.1875, 225], [95.1875, 250], [80.5, 250], [80.5, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-5": L.polygon([[95.1875, 225], [109.875, 225], [109.875, 250], [95.1875, 250], [95.1875, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-6": L.polygon([[109.875, 225], [124.5625, 225], [124.5625, 250], [109.875, 250], [109.875, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-7": L.polygon([[124.5625, 225], [139.25, 225], [139.25, 250], [124.5625, 250], [124.5625, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-8": L.polygon([[139.25, 225], [153.9375, 225], [153.9375, 250], [139.25, 250], [139.25, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-9": L.polygon([[153.9375, 225], [168.625, 225], [168.625, 250], [153.9375, 250], [153.9375, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-10": L.polygon([[196.25, 225], [228.0416666666667, 225], [228.0416666666667, 250], [196.25, 250], [196.25, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-11": L.polygon([[228.0416666666667, 225], [259.8333333333333, 225], [259.8333333333333, 250], [228.0416666666667, 250], [228.0416666666667, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-12": L.polygon([[259.8333333333333, 225], [291.625, 225], [291.625, 250], [259.8333333333333, 250], [259.8333333333333, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-13": L.polygon([[291.625, 225], [323.4166666666667, 225], [323.4166666666667, 250], [291.625, 250], [291.625, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-14": L.polygon([[323.4166666666667, 225], [355.2083333333334, 225], [355.2083333333334, 250], [323.4166666666667, 250], [323.4166666666667, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-15": L.polygon([[355.2083333333334, 225], [387, 225], [387, 250], [355.2083333333334, 250], [355.2083333333334, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-16": L.polygon([[387, 225], [418.7916666666667, 225], [418.7916666666667, 250], [387, 250], [387, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-17": L.polygon([[418.7916666666667, 225], [482.375, 225], [482.375, 250], [418.7916666666667, 250], [418.7916666666667, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-18": L.polygon([[492.375, 225], [515.875, 225], [515.875, 300], [492.375, 300], [492.375, 225]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-19": L.polygon([[0, 275], [17.625, 275], [17.625, 300], [0, 300], [0, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-20": L.polygon([[17.625, 275], [35.25, 275], [35.25, 300], [17.625, 300], [17.625, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-21": L.polygon([[80.5, 275], [98.125, 275], [98.125, 300], [80.5, 300], [80.5, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-22": L.polygon([[125.75, 275], [149.25, 275], [149.25, 300], [125.75, 300], [125.75, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-23": L.polygon([[149.25, 275], [172.75, 275], [172.75, 300], [149.25, 300], [149.25, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-24": L.polygon([[172.75, 275], [196.25, 275], [196.25, 300], [172.75, 300], [172.75, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-25": L.polygon([[196.25, 275], [219.75, 275], [219.75, 300], [196.25, 300], [196.25, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-26": L.polygon([[219.75, 275], [243.25, 275], [243.25, 300], [219.75, 300], [219.75, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-27": L.polygon([[243.25, 275], [266.75, 275], [266.75, 300], [243.25, 300], [243.25, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-28": L.polygon([[266.75, 275], [290.25, 275], [290.25, 300], [266.75, 300], [266.75, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-29": L.polygon([[290.25, 275], [313.75, 275], [313.75, 300], [290.25, 300], [290.25, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-30": L.polygon([[313.75, 275], [337.25, 275], [337.25, 300], [313.75, 300], [313.75, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-31": L.polygon([[364.875, 275], [388.375, 275], [388.375, 300], [364.875, 300], [364.875, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-32": L.polygon([[388.375, 275], [411.875, 275], [411.875, 300], [388.375, 300], [388.375, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-33": L.polygon([[411.875, 275], [435.375, 275], [435.375, 300], [411.875, 300], [411.875, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-34": L.polygon([[435.375, 275], [458.875, 275], [458.875, 300], [435.375, 300], [435.375, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P3-35": L.polygon([[458.875, 275], [482.375, 275], [482.375, 300], [458.875, 300], [458.875, 275]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),

        "P4-1": L.polygon([[0, 300], [17.625, 300], [17.625, 325], [0, 325], [0, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-2": L.polygon([[17.625, 300], [35.25, 300], [35.25, 325], [17.625, 325], [17.625, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-3": L.polygon([[62.875, 300], [98.125, 300], [98.125, 325], [62.875, 325], [62.875, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-4": L.polygon([[125.75, 300], [149.25, 300], [149.25, 325], [125.75, 325], [125.75, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-5": L.polygon([[149.25, 300], [172.75, 300], [172.75, 325], [149.25, 325], [149.25, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-6": L.polygon([[172.75, 300], [196.25, 300], [196.25, 325], [172.75, 325], [172.75, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-7": L.polygon([[196.25, 300], [219.75, 300], [219.75, 325], [196.25, 325], [196.25, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-8": L.polygon([[219.75, 300], [243.25, 300], [243.25, 325], [219.75, 325], [219.75, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-9": L.polygon([[243.25, 300], [266.75, 300], [266.75, 325], [243.25, 325], [243.25, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-10": L.polygon([[266.75, 300], [290.25, 300], [290.25, 325], [266.75, 325], [266.75, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-11": L.polygon([[290.25, 300], [313.75, 300], [313.75, 325], [290.25, 325], [290.25, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-12": L.polygon([[313.75, 300], [337.25, 300], [337.25, 325], [313.75, 325], [313.75, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-13": L.polygon([[364.875, 300], [411.875, 300], [411.875, 325], [364.875, 325], [364.875, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-14": L.polygon([[411.875, 300], [435.375, 300], [435.375, 325], [411.875, 325], [411.875, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-15": L.polygon([[435.375, 300], [458.875, 300], [458.875, 325], [435.375, 325], [435.375, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-16": L.polygon([[492.375, 300], [515.875, 300], [515.875, 350], [492.375, 350], [492.375, 300]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-17": L.polygon([[0, 350], [26.99264705882353, 350], [26.99264705882353, 375], [0, 375], [0, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-18": L.polygon([[26.99264705882353, 350], [53.98529411764706, 350], [53.98529411764706, 375], [26.99264705882353, 375], [26.99264705882353, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-19": L.polygon([[53.98529411764706, 350], [80.97794117647059, 350], [80.97794117647059, 375], [53.98529411764706, 375], [53.98529411764706, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-20": L.polygon([[80.97794117647059, 350], [107.9705882352941, 350], [107.9705882352941, 375], [80.97794117647059, 375], [80.97794117647059, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-21": L.polygon([[107.9705882352941, 350], [134.9632352941177, 350], [134.9632352941177, 375], [107.9705882352941, 375], [107.9705882352941, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-22": L.polygon([[134.9632352941177, 350], [161.9558823529412, 350], [161.9558823529412, 375], [134.9632352941177, 375], [134.9632352941177, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-23": L.polygon([[161.9558823529412, 350], [188.9485294117648, 350], [188.9485294117648, 375], [161.9558823529412, 375], [161.9558823529412, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-24": L.polygon([[215.9411764705883, 350], [242.9338235294118, 350], [242.9338235294118, 375], [215.9411764705883, 375], [215.9411764705883, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-25": L.polygon([[242.9338235294118, 350], [269.9264705882354, 350], [269.9264705882354, 375], [242.9338235294118, 375], [242.9338235294118, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-26": L.polygon([[269.9264705882354, 350], [296.9191176470589, 350], [296.9191176470589, 375], [269.9264705882354, 375], [269.9264705882354, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-27": L.polygon([[296.9191176470589, 350], [323.9117647058824, 350], [323.9117647058824, 375], [296.9191176470589, 375], [296.9191176470589, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-28": L.polygon([[323.9117647058824, 350], [350.9044117647059, 350], [350.9044117647059, 375], [323.9117647058824, 375], [323.9117647058824, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-29": L.polygon([[350.9044117647059, 350], [377.8970588235295, 350], [377.8970588235295, 375], [350.9044117647059, 375], [350.9044117647059, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-30": L.polygon([[377.8970588235295, 350], [404.889705882353, 350], [404.889705882353, 375], [377.8970588235295, 375], [377.8970588235295, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-31": L.polygon([[404.889705882353, 350], [431.8823529411766, 350], [431.8823529411766, 375], [404.889705882353, 375], [404.889705882353, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
        "P4-32": L.polygon([[431.8823529411766, 350], [458.8750000000001, 350], [458.8750000000001, 375], [431.8823529411766, 375], [431.8823529411766, 350]], vacant).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' })
    };

    var stair = {
      "P2-stair": L.polygon([[0, 150], [17.625, 150], [17.625, 175], [0, 175], [0, 150]], stair).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
      "P3-stair": L.polygon([[62.875, 275],[80.5, 275],[80.5, 300],[62.875, 300],[62.875, 275]], stair).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
      "P4-stair": L.polygon([[188.9485294117648, 350], [215.9411764705883, 350], [215.9411764705883, 375], [188.9485294117648, 375], [188.9485294117648, 350]], stair).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' })
    };

    var freezer = {
      "P4-freezer": L.polygon([[492.375, 350], [515.875, 350], [515.875, 375], [492.375, 375], [492.375, 350]], freezer).bindTooltip("", { permanent: true, direction: 'center', className: 'tooltipclass' }),
    };


    /*
    var p1_1 = L.polygon([
      [0,  0],
      [23.5, 0],
      [23.5, 25],
      [0, 25],
      [0,  0]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p1_2 = L.polygon([
      [23.5, 0],
      [47, 0],
      [47, 25],
      [23.5, 25],
      [23.5, 0]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p1_3 = L.polygon([
      [47, 0],
      [70.5, 0],
      [70.5, 25],
      [47, 25],
      [47, 0]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p1_4 = L.polygon([
      [98.125, 0],
      [146.15625, 0],
      [146.15625, 25],
      [98.125, 25],
      [98.125, 0]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p1_5 = L.polygon([
      [146.15625, 0],
      [194.1875, 0],
      [194.1875, 25],
      [146.15625, 25],
      [146.15625, 0]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p1_6 = L.polygon([
      [194.1875, 0],
      [242.21875, 0],
      [242.21875, 25],
      [194.1875, 25],
      [194.1875, 0]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p1_7 = L.polygon([
      [242.21875, 0],
      [290.25, 0],
      [290.25, 25],
      [242.21875, 25],
      [242.21875, 0]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p1_8 = L.polygon([
      [0, 50],
      [17.625, 50],
      [17.625, 75],
      [0, 75],
      [0, 50]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p1_9 = L.polygon([
      [17.625, 50],
      [35.25, 50],
      [35.25, 75],
      [17.625, 75],
      [17.625, 50]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p1_10 = L.polygon([
      [35.25, 50],
      [52.875, 50],
      [52.875, 75],
      [35.25, 75],
      [35.25, 50]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p1_11 = L.polygon([
      [52.875, 50],
      [70.5, 50],
      [70.5, 75],
      [52.875, 75],
      [52.875, 50]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p1_12 = L.polygon([
      [98.125, 50],
      [146.15625, 50],
      [146.15625, 75],
      [98.125, 75],
      [98.125, 50]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p1_13 = L.polygon([
      [146.15625, 50],
      [194.1875, 50],
      [194.1875, 75],
      [146.15625, 75],
      [146.15625, 50]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p1_14 = L.polygon([
      [194.1875, 50],
      [242.21875, 50],
      [242.21875, 75],
      [194.1875, 75],
      [194.1875, 50]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p1_15 = L.polygon([
      [242.21875, 50],
      [290.25, 50],
      [290.25, 75],
      [242.21875, 75],
      [242.21875, 50]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var phaseOne = L.layerGroup([p1_1, p1_2, p1_3, p1_4, p1_5, p1_6, p1_7, p1_8, p1_9, p1_10, p1_11, p1_12, p1_13, p1_14, p1_15]);

    var p2_1 = L.polygon([
      [0, 75],
      [23.5, 75],
      [23.5, 100],
      [0, 100],
      [0, 75]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_2 = L.polygon([
      [23.5, 75],
      [47, 75],
      [47, 100],
      [23.5, 100],
      [23.5, 75]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_3 = L.polygon([
      [47, 75],
      [70.5, 75],
      [70.5, 100],
      [47, 100],
      [47, 75]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_4 = L.polygon([
      [98.125, 75],
      [115.75, 75],
      [115.75, 100],
      [98.125, 100],
      [98.125, 75]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_5 = L.polygon([
      [115.75, 75],
      [133.375, 75],
      [133.375, 100],
      [115.75, 100],
      [115.75, 75]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_6 = L.polygon([
      [133.375, 75],
      [151, 75],
      [151, 100],
      [133.375, 100],
      [133.375, 75]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_7 = L.polygon([
      [151, 75],
      [168.625, 75],
      [168.625, 100],
      [151, 100],
      [151, 75]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_8 = L.polygon([
      [168.625, 75],
      [186.25, 75],
      [186.25, 100],
      [168.625, 100],
      [168.625, 75]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_9 = L.polygon([
      [267.625, 75],
      [290.25, 75],
      [290.25, 110],
      [267.625, 110],
      [267.625, 75]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_10 = L.polygon([
      [0, 125],
      [17.625, 125],
      [17.625, 150],
      [0, 150],
      [0, 125]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_11 = L.polygon([
      [17.625, 125],
      [35.25, 125],
      [35.25, 150],
      [17.625, 150],
      [17.625, 125]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_12 = L.polygon([
      [35.25, 125],
      [52.875, 125],
      [52.875, 150],
      [35.25, 150],
      [35.25, 125]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_13 = L.polygon([
      [52.875, 125],
      [70.5, 125],
      [70.5, 150],
      [52.875, 150],
      [52.875, 125]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_14 = L.polygon([
      [80.5, 125],
      [98.125, 125],
      [98.125, 150],
      [80.5, 150],
      [80.5, 125]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_15 = L.polygon([
      [98.125, 125],
      [115.75, 125],
      [115.75, 150],
      [98.125, 150],
      [98.125, 125]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_16 = L.polygon([
      [115.75, 125],
      [133.375, 125],
      [133.375, 150],
      [115.75, 150],
      [115.75, 125]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_17 = L.polygon([
      [133.375, 125],
      [151, 125],
      [151, 150],
      [133.375, 150],
      [133.375, 125]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_18 = L.polygon([
      [151, 125],
      [168.625, 125],
      [168.625, 150],
      [151, 150],
      [151, 125]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_19 = L.polygon([
      [196.25, 125],
      [243.25, 125],
      [243.25, 150],
      [196.25, 150],
      [196.25, 125]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_stair = L.polygon([
      [0, 150],
      [17.625, 150],
      [17.625, 175],
      [0, 175],
      [0, 150]
    ], stair
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_20 = L.polygon([
      [17.625, 150],
      [35.25, 150],
      [35.25, 175],
      [17.625, 175],
      [17.625, 150]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_21 = L.polygon([
      [35.25, 150],
      [70.5, 150],
      [70.5, 175],
      [35.25, 175],
      [35.25, 150]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_22 = L.polygon([
      [80.5, 150],
      [98.125, 150],
      [98.125, 175],
      [80.5, 175],
      [80.5, 150]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_23 = L.polygon([
      [98.125, 150],
      [115.75, 150],
      [115.75, 175],
      [98.125, 175],
      [98.125, 150]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_24 = L.polygon([
      [115.75, 150],
      [133.375, 150],
      [133.375, 175],
      [115.75, 175],
      [115.75, 150]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_25 = L.polygon([
      [133.375, 150],
      [151, 150],
      [151, 175],
      [133.375, 175],
      [133.375, 150]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_26 = L.polygon([
      [151, 150],
      [168.625, 150],
      [168.625, 175],
      [151, 175],
      [151, 150]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_27 = L.polygon([
      [196.25, 150],
      [243.25, 150],
      [243.25, 175],
      [196.25, 175],
      [196.25, 150]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_28 = L.polygon([
      [243.25, 150],
      [290.25, 150],
      [290.25, 175],
      [243.25, 175],
      [243.25, 150]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_29 = L.polygon([
      [0, 200],
      [17.625, 200],
      [17.625, 225],
      [0, 225],
      [0, 200]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_30 = L.polygon([
      [17.625, 200],
      [35.25, 200],
      [35.25, 225],
      [17.625, 225],
      [17.625, 200]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_31 = L.polygon([
      [35.25, 200],
      [52.875, 200],
      [52.875, 225],
      [35.25, 225],
      [35.25, 200]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_32 = L.polygon([
      [52.875, 200],
      [70.5, 200],
      [70.5, 225],
      [52.875, 225],
      [52.875, 200]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_33 = L.polygon([
      [80.5, 200],
      [98.125, 200],
      [98.125, 225],
      [80.5, 225],
      [80.5, 200]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_34 = L.polygon([
      [98.125, 200],
      [115.75, 200],
      [115.75, 225],
      [98.125, 225],
      [98.125, 200]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_35 = L.polygon([
      [115.75, 200],
      [133.375, 200],
      [133.375, 225],
      [115.75, 225],
      [115.75, 200]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_36 = L.polygon([
      [133.375, 200],
      [151, 200],
      [151, 225],
      [133.375, 225],
      [133.375, 200]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_37 = L.polygon([
      [151, 200],
      [168.625, 200],
      [168.625, 225],
      [151, 225],
      [151, 200]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_38 = L.polygon([
      [196.25, 187.5],
      [243.25, 187.5],
      [243.25, 212.5],
      [196.25, 212.5],
      [196.25, 187.5]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p2_39 = L.polygon([
      [243.25, 187.5],
      [290.25, 187.5],
      [290.25, 212.5],
      [243.25, 212.5],
      [243.25, 187.5]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var phaseTwo = L.layerGroup([p2_1, p2_2, p2_3, p2_4, p2_5, p2_6, p2_7, p2_8, p2_9, p2_10, p2_11, p2_12, p2_13, p2_14, p2_15, p2_16, p2_17, p2_18, p2_19, p2_stair, p2_20, p2_21,
    p2_22, p2_23, p2_24, p2_25, p2_26, p2_27, p2_28, p2_29, p2_30, p2_31, p2_32, p2_33, p2_34, p2_35, p2_36, p2_37, p2_38, p2_39]);

    var p3_1 = L.polygon([
      [0, 225],
      [23.5, 225],
      [23.5, 250],
      [0, 250],
      [0, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_2 = L.polygon([
      [23.5, 225],
      [47, 225],
      [47, 250],
      [23.5, 250],
      [23.5, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_3 = L.polygon([
      [47, 225],
      [70.5, 225],
      [70.5, 250],
      [47, 250],
      [47, 225]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_4 = L.polygon([
      [80.5, 225],
      [95.1875, 225],
      [95.1875, 250],
      [80.5, 250],
      [80.5, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_5 = L.polygon([
      [95.1875, 225],
      [109.875, 225],
      [109.875, 250],
      [95.1875, 250],
      [95.1875, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_6 = L.polygon([
      [109.875, 225],
      [124.5625, 225],
      [124.5625, 250],
      [109.875, 250],
      [109.875, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_7 = L.polygon([
      [124.5625, 225],
      [139.25, 225],
      [139.25, 250],
      [124.5625, 250],
      [124.5625, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_8 = L.polygon([
      [139.25, 225],
      [153.9375, 225],
      [153.9375, 250],
      [139.25, 250],
      [139.25, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_9 = L.polygon([
      [153.9375, 225],
      [168.625, 225],
      [168.625, 250],
      [153.9375, 250],
      [153.9375, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_10 = L.polygon([
      [196.25, 225],
      [228.0416666666667, 225],
      [228.0416666666667, 250],
      [196.25, 250],
      [196.25, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_11 = L.polygon([
      [228.0416666666667, 225],
      [259.8333333333333, 225],
      [259.8333333333333, 250],
      [228.0416666666667, 250],
      [228.0416666666667, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_12 = L.polygon([
      [259.8333333333333, 225],
      [291.625, 225],
      [291.625, 250],
      [259.8333333333333, 250],
      [259.8333333333333, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_13 = L.polygon([
      [291.625, 225],
      [323.4166666666667, 225],
      [323.4166666666667, 250],
      [291.625, 250],
      [291.625, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_14 = L.polygon([
      [323.4166666666667, 225],
      [355.2083333333334, 225],
      [355.2083333333334, 250],
      [323.4166666666667, 250],
      [323.4166666666667, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_15 = L.polygon([
      [355.2083333333334, 225],
      [387, 225],
      [387, 250],
      [355.2083333333334, 250],
      [355.2083333333334, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_16 = L.polygon([
      [387, 225],
      [418.7916666666667, 225],
      [418.7916666666667, 250],
      [387, 250],
      [387, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_17 = L.polygon([
      [418.7916666666667, 225],
      [482.375, 225],
      [482.375, 250],
      [418.7916666666667, 250],
      [418.7916666666667, 225]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_18 = L.polygon([
      [492.375, 225],
      [515.875, 225],
      [515.875, 300],
      [492.375, 300],
      [492.375, 225]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_19 = L.polygon([
      [0, 275],
      [17.625, 275],
      [17.625, 300],
      [0, 300],
      [0, 275]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_20 = L.polygon([
      [17.625, 275],
      [35.25, 275],
      [35.25, 300],
      [17.625, 300],
      [17.625, 275]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_stair = L.polygon([
      [62.875, 275],
      [80.5, 275],
      [80.5, 300],
      [62.875, 300],
      [62.875, 275]
    ], stair
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_21 = L.polygon([
      [80.5, 275],
      [98.125, 275],
      [98.125, 300],
      [80.5, 300],
      [80.5, 275]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_22 = L.polygon([
      [125.75, 275],
      [149.25, 275],
      [149.25, 300],
      [125.75, 300],
      [125.75, 275]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_23 = L.polygon([
      [149.25, 275],
      [172.75, 275],
      [172.75, 300],
      [149.25, 300],
      [149.25, 275]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_24 = L.polygon([
      [172.75, 275],
      [196.25, 275],
      [196.25, 300],
      [172.75, 300],
      [172.75, 275]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_25 = L.polygon([
      [196.25, 275],
      [219.75, 275],
      [219.75, 300],
      [196.25, 300],
      [196.25, 275]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_26 = L.polygon([
      [219.75, 275],
      [243.25, 275],
      [243.25, 300],
      [219.75, 300],
      [219.75, 275]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_27 = L.polygon([
      [243.25, 275],
      [266.75, 275],
      [266.75, 300],
      [243.25, 300],
      [243.25, 275]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_28 = L.polygon([
      [266.75, 275],
      [290.25, 275],
      [290.25, 300],
      [266.75, 300],
      [266.75, 275]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_29 = L.polygon([
      [290.25, 275],
      [313.75, 275],
      [313.75, 300],
      [290.25, 300],
      [290.25, 275]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_30 = L.polygon([
      [313.75, 275],
      [337.25, 275],
      [337.25, 300],
      [313.75, 300],
      [313.75, 275]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_31 = L.polygon([
      [364.875, 275],
      [388.375, 275],
      [388.375, 300],
      [364.875, 300],
      [364.875, 275]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_32 = L.polygon([
      [388.375, 275],
      [411.875, 275],
      [411.875, 300],
      [388.375, 300],
      [388.375, 275]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_33 = L.polygon([
      [411.875, 275],
      [435.375, 275],
      [435.375, 300],
      [411.875, 300],
      [411.875, 275]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_34 = L.polygon([
      [435.375, 275],
      [458.875, 275],
      [458.875, 300],
      [435.375, 300],
      [435.375, 275]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p3_35 = L.polygon([
      [458.875, 275],
      [482.375, 275],
      [482.375, 300],
      [458.875, 300],
      [458.875, 275]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var phaseThree = L.layerGroup([p3_1, p3_2, p3_3, p3_4, p3_5, p3_6, p3_7, p3_8, p3_9, p3_10, p3_11, p3_12, p3_13, p3_14, p3_15, p3_16, p3_17, p3_18, p3_19, p3_20,
    p3_stair, p3_21, p3_22, p3_23, p3_24, p3_25, p3_26, p3_27, p3_28, p3_29, p3_30, p3_31, p3_32, p3_33, p3_34, p3_35]);



    var p4_1 = L.polygon([
      [0, 300],
      [17.625, 300],
      [17.625, 325],
      [0, 325],
      [0, 300]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_2 = L.polygon([
      [17.625, 300],
      [35.25, 300],
      [35.25, 325],
      [17.625, 325],
      [17.625, 300]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_3 = L.polygon([
      [62.875, 300],
      [98.125, 300],
      [98.125, 325],
      [62.875, 325],
      [62.875, 300]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_4 = L.polygon([
      [125.75, 300],
      [149.25, 300],
      [149.25, 325],
      [125.75, 325],
      [125.75, 300]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_5 = L.polygon([
      [149.25, 300],
      [172.75, 300],
      [172.75, 325],
      [149.25, 325],
      [149.25, 300]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_6 = L.polygon([
      [172.75, 300],
      [196.25, 300],
      [196.25, 325],
      [172.75, 325],
      [172.75, 300]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_7 = L.polygon([
      [196.25, 300],
      [219.75, 300],
      [219.75, 325],
      [196.25, 325],
      [196.25, 300]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_8 = L.polygon([
      [219.75, 300],
      [243.25, 300],
      [243.25, 325],
      [219.75, 325],
      [219.75, 300]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_9 = L.polygon([
      [243.25, 300],
      [266.75, 300],
      [266.75, 325],
      [243.25, 325],
      [243.25, 300]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_10 = L.polygon([
      [266.75, 300],
      [290.25, 300],
      [290.25, 325],
      [266.75, 325],
      [266.75, 300]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_11 = L.polygon([
      [290.25, 300],
      [313.75, 300],
      [313.75, 325],
      [290.25, 325],
      [290.25, 300]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_12 = L.polygon([
      [313.75, 300],
      [337.25, 300],
      [337.25, 325],
      [313.75, 325],
      [313.75, 300]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_13 = L.polygon([
      [364.875, 300],
      [411.875, 300],
      [411.875, 325],
      [364.875, 325],
      [364.875, 300]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_14 = L.polygon([
      [411.875, 300],
      [435.375, 300],
      [435.375, 325],
      [411.875, 325],
      [411.875, 300]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_15 = L.polygon([
      [435.375, 300],
      [458.875, 300],
      [458.875, 325],
      [435.375, 325],
      [435.375, 300]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_16 = L.polygon([
      [492.375, 300],
      [515.875, 300],
      [515.875, 350],
      [492.375, 350],
      [492.375, 300]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_freezer = L.polygon([
      [492.375, 350],
      [515.875, 350],
      [515.875, 375],
      [492.375, 375],
      [492.375, 350]
    ], freezer
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_17 = L.polygon([
      [0,  350],
      [26.99264705882353, 350],
      [26.99264705882353, 375],
      [0, 375],
      [0, 350]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_18 = L.polygon([
      [26.99264705882353, 350],
      [53.98529411764706, 350],
      [53.98529411764706, 375],
      [26.99264705882353, 375],
      [26.99264705882353, 350]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_19 = L.polygon([
      [53.98529411764706, 350],
      [80.97794117647059, 350],
      [80.97794117647059, 375],
      [53.98529411764706, 375],
      [53.98529411764706, 350]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_20 = L.polygon([
      [80.97794117647059, 350],
      [107.9705882352941, 350],
      [107.9705882352941, 375],
      [80.97794117647059, 375],
      [80.97794117647059, 350]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_21 = L.polygon([
      [107.9705882352941, 350],
      [134.9632352941177, 350],
      [134.9632352941177, 375],
      [107.9705882352941, 375],
      [107.9705882352941, 350]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_22 = L.polygon([
      [134.9632352941177, 350],
      [161.9558823529412, 350],
      [161.9558823529412, 375],
      [134.9632352941177, 375],
      [134.9632352941177, 350]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_23 = L.polygon([
      [161.9558823529412, 350],
      [188.9485294117648, 350],
      [188.9485294117648, 375],
      [161.9558823529412, 375],
      [161.9558823529412, 350]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_stair = L.polygon([
      [188.9485294117648, 350],
      [215.9411764705883, 350],
      [215.9411764705883, 375],
      [188.9485294117648, 375],
      [188.9485294117648, 350]
    ], stair
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_24 = L.polygon([
      [215.9411764705883, 350],
      [242.9338235294118, 350],
      [242.9338235294118, 375],
      [215.9411764705883, 375],
      [215.9411764705883, 350]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_25 = L.polygon([
      [242.9338235294118, 350],
      [269.9264705882354, 350],
      [269.9264705882354, 375],
      [242.9338235294118, 375],
      [242.9338235294118, 350]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_26 = L.polygon([
      [269.9264705882354, 350],
      [296.9191176470589, 350],
      [296.9191176470589, 375],
      [269.9264705882354, 375],
      [269.9264705882354, 350]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_27 = L.polygon([
      [296.9191176470589, 350],
      [323.9117647058824, 350],
      [323.9117647058824, 375],
      [296.9191176470589, 375],
      [296.9191176470589, 350]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_28 = L.polygon([
      [323.9117647058824, 350],
      [350.9044117647059, 350],
      [350.9044117647059, 375],
      [323.9117647058824, 375],
      [323.9117647058824, 350]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_29 = L.polygon([
      [350.9044117647059, 350],
      [377.8970588235295, 350],
      [377.8970588235295, 375],
      [350.9044117647059, 375],
      [350.9044117647059, 350]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_30 = L.polygon([
      [377.8970588235295, 350],
      [404.889705882353, 350],
      [404.889705882353, 375],
      [377.8970588235295, 375],
      [377.8970588235295, 350]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_31 = L.polygon([
      [404.889705882353, 350],
      [431.8823529411766, 350],
      [431.8823529411766, 375],
      [404.889705882353, 375],
      [404.889705882353, 350]
    ], vacant
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var p4_32 = L.polygon([
      [431.8823529411766, 350],
      [458.8750000000001, 350],
      [458.8750000000001, 375],
      [431.8823529411766, 375],
      [431.8823529411766, 350]
    ], occupied
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });

    var phaseFour = L.layerGroup([p4_1, p4_2, p4_3, p4_4, p4_5, p4_6, p4_7, p4_8, p4_9, p4_10, p4_11, p4_12, p4_13, p4_14, p4_15, p4_16, p4_freezer, p4_17, p4_18, p4_19, p4_20, p4_21, p4_22, p4_23, p4_stair, p4_24, p4_25, p4_26, p4_27, p4_28, p4_29, p4_30, p4_31, p4_32,]);
*/
    /*
    var phaseOne = L.polygon([
      [[0,  0],
      [23.5, 0],
      [23.5, 25],
      [0, 25],
      [0, 0]],
      
      [[23.5, 0],
      [47, 0],
      [47, 25],
      [23.5, 25],
      [23.5, 0]],
      
      [[47, 0],
      [70.5, 0],
      [70.5, 25],
      [47, 25],
      [47, 0]],

      [[98.125, 0],
      [146.15625, 0],
      [146.15625, 25],
      [98.125, 25],
      [98.125, 0]],

      [[146.15625, 0],
      [194.1875, 0],
      [194.1875, 25],
      [146.15625, 25],
      [146.15625, 0]],

      [[194.1875, 0],
      [242.21875, 0],
      [242.21875, 25],
      [194.1875, 25],
      [194.1875, 0]],

      [[242.21875, 0],
      [290.25, 0],
      [290.25, 25],
      [242.21875, 25],
      [242.21875, 0]],





      [[0, 50],
      [17.625, 50],
      [17.625, 75],
      [0, 75],
      [0, 50]],

      [[17.625, 50],
      [35.25, 50],
      [35.25, 75],
      [17.625, 75],
      [17.625, 50]],

      [[35.25, 50],
      [52.875, 50],
      [52.875, 75],
      [35.25, 75],
      [35.25, 50]],

      [[52.875, 50],
      [70.5, 50],
      [70.5, 75],
      [52.875, 75],
      [52.875, 50]],

      [[98.125, 50],
      [146.15625, 50],
      [146.15625, 75],
      [98.125, 75],
      [98.125, 50]],

      [[146.15625, 50],
      [194.1875, 50],
      [194.1875, 75],
      [146.15625, 75],
      [146.15625, 50]],

      [[194.1875, 50],
      [242.21875, 50],
      [242.21875, 75],
      [194.1875, 75],
      [194.1875, 50]],

      [[242.21875, 50],
      [290.25, 50],
      [290.25, 75],
      [242.21875, 75],
      [242.21875, 50]],
    ],
    solidWhiteLineBeigeFill
    ).bindTooltip("", {
      permanent: true, 
      direction: 'center', 
      className: 'tooltipclass'
    });
    

    var phaseTwo = L.polygon([
      [[0, 75],
      [23.5, 75],
      [23.5, 100],
      [0, 100],
      [0, 75]],

      [[23.5, 75],
      [47, 75],
      [47, 100],
      [23.5, 100],
      [23.5, 75]],

      [[47, 75],
      [70.5, 75],
      [70.5, 100],
      [47, 100],
      [47, 75]],

      [[98.125, 75],
      [115.75, 75],
      [115.75, 100],
      [98.125, 100],
      [98.125, 75]],

      [[115.75, 75],
      [133.375, 75],
      [133.375, 100],
      [115.75, 100],
      [115.75, 75]],

      [[133.375, 75],
      [151, 75],
      [151, 100],
      [133.375, 100],
      [133.375, 75]],

      [[151, 75],
      [168.625, 75],
      [168.625, 100],
      [151, 100],
      [151, 75]],

      [[168.625, 75],
      [186.25, 75],
      [186.25, 100],
      [168.625, 100],
      [168.625, 75]],

      [[267.625, 75],
      [290.25, 75],
      [290.25, 110],
      [267.625, 110],
      [267.625, 75]],




      
      [[0, 125],
      [17.625, 125],
      [17.625, 150],
      [0, 150],
      [0, 125]],

      [[17.625, 125],
      [35.25, 125],
      [35.25, 150],
      [17.625, 150],
      [17.625, 125]],

      [[35.25, 125],
      [52.875, 125],
      [52.875, 150],
      [35.25, 150],
      [35.25, 125]],

      [[52.875, 125],
      [70.5, 125],
      [70.5, 150],
      [52.875, 150],
      [52.875, 125]],

      [[80.5, 125],
      [98.125, 125],
      [98.125, 150],
      [80.5, 150],
      [80.5, 125]],

      [[98.125, 125],
      [115.75, 125],
      [115.75, 150],
      [98.125, 150],
      [98.125, 125]],

      [[115.75, 125],
      [133.375, 125],
      [133.375, 150],
      [115.75, 150],
      [115.75, 125]],

      [[133.375, 125],
      [151, 125],
      [151, 150],
      [133.375, 150],
      [133.375, 125]],

      [[151, 125],
      [168.625, 125],
      [168.625, 150],
      [151, 150],
      [151, 125]],

      [[196.25, 125],
      [243.25, 125],
      [243.25, 150],
      [196.25, 150],
      [196.25, 125]],





      [[0, 150],
      [17.625, 150],
      [17.625, 175],
      [0, 175],
      [0, 150]],

      [[17.625, 150],
      [35.25, 150],
      [35.25, 175],
      [17.625, 175],
      [17.625, 150]],

      [[35.25, 150],
      [70.5, 150],
      [70.5, 175],
      [35.25, 175],
      [35.25, 150]],

      [[80.5, 150],
      [98.125, 150],
      [98.125, 175],
      [80.5, 175],
      [80.5, 150]],

      [[98.125, 150],
      [115.75, 150],
      [115.75, 175],
      [98.125, 175],
      [98.125, 150]],

      [[115.75, 150],
      [133.375, 150],
      [133.375, 175],
      [115.75, 175],
      [115.75, 150]],

      [[133.375, 150],
      [151, 150],
      [151, 175],
      [133.375, 175],
      [133.375, 150]],

      [[151, 150],
      [168.625, 150],
      [168.625, 175],
      [151, 175],
      [151, 150]],

      [[196.25, 150],
      [243.25, 150],
      [243.25, 175],
      [196.25, 175],
      [196.25, 150]],

      [[243.25, 150],
      [290.25, 150],
      [290.25, 175],
      [243.25, 175],
      [243.25, 150]],





      [[0, 200],
      [17.625, 200],
      [17.625, 225],
      [0, 225],
      [0, 200]],

      [[17.625, 200],
      [35.25, 200],
      [35.25, 225],
      [17.625, 225],
      [17.625, 200]],

      [[35.25, 200],
      [52.875, 200],
      [52.875, 225],
      [35.25, 225],
      [35.25, 200]],

      [[52.875, 200],
      [70.5, 200],
      [70.5, 225],
      [52.875, 225],
      [52.875, 200]],

      [[80.5, 200],
      [98.125, 200],
      [98.125, 225],
      [80.5, 225],
      [80.5, 200]],

      [[98.125, 200],
      [115.75, 200],
      [115.75, 225],
      [98.125, 225],
      [98.125, 200]],

      [[115.75, 200],
      [133.375, 200],
      [133.375, 225],
      [115.75, 225],
      [115.75, 200]],

      [[133.375, 200],
      [151, 200],
      [151, 225],
      [133.375, 225],
      [133.375, 200]],

      [[151, 200],
      [168.625, 200],
      [168.625, 225],
      [151, 225],
      [151, 200]],

      [[196.25, 187.5],
      [243.25, 187.5],
      [243.25, 212.5],
      [196.25, 212.5],
      [196.25, 187.5]],

      [[243.25, 187.5],
      [290.25, 187.5],
      [290.25, 212.5],
      [243.25, 212.5],
      [243.25, 187.5]],
      
    ],
    solidWhiteLineBeigeFill
    ).bindPopup("Phase Two");
    

    var phaseThree = L.polygon([
      [[0, 225],
      [23.5, 225],
      [23.5, 250],
      [0, 250],
      [0, 225]],

      [[23.5, 225],
      [47, 225],
      [47, 250],
      [23.5, 250],
      [23.5, 225]],

      [[47, 225],
      [70.5, 225],
      [70.5, 250],
      [47, 250],
      [47, 225]],

      [[80.5, 225],
      [95.1875, 225],
      [95.1875, 250],
      [80.5, 250],
      [80.5, 225]],

      [[95.1875, 225],
      [109.875, 225],
      [109.875, 250],
      [95.1875, 250],
      [95.1875, 225]],

      [[109.875, 225],
      [124.5625, 225],
      [124.5625, 250],
      [109.875, 250],
      [109.875, 225]],

      [[124.5625, 225],
      [139.25, 225],
      [139.25, 250],
      [124.5625, 250],
      [124.5625, 225]],

      [[139.25, 225],
      [153.9375, 225],
      [153.9375, 250],
      [139.25, 250],
      [139.25, 225]],

      [[153.9375, 225],
      [168.625, 225],
      [168.625, 250],
      [153.9375, 250],
      [153.9375, 225]],


      [[196.25, 225],
      [228.0416666666667, 225],
      [228.0416666666667, 250],
      [196.25, 250],
      [196.25, 225]],

      [[228.0416666666667, 225],
      [259.8333333333333, 225],
      [259.8333333333333, 250],
      [228.0416666666667, 250],
      [228.0416666666667, 225]],

      [[259.8333333333333, 225],
      [291.625, 225],
      [291.625, 250],
      [259.8333333333333, 250],
      [259.8333333333333, 225]],

      [[291.625, 225],
      [323.4166666666667, 225],
      [323.4166666666667, 250],
      [291.625, 250],
      [291.625, 225]],

      [[323.4166666666667, 225],
      [355.2083333333334, 225],
      [355.2083333333334, 250],
      [323.4166666666667, 250],
      [323.4166666666667, 225]],

      [[355.2083333333334, 225],
      [387, 225],
      [387, 250],
      [355.2083333333334, 250],
      [355.2083333333334, 225]],

      [[387, 225],
      [418.7916666666667, 225],
      [418.7916666666667, 250],
      [387, 250],
      [387, 225]],

      [[418.7916666666667, 225],
      [482.375, 225],
      [482.375, 250],
      [418.7916666666667, 250],
      [418.7916666666667, 225]],

      [[492.375, 225],
      [515.875, 225],
      [515.875, 300],
      [492.375, 300],
      [492.375, 225]],
      


      [[0, 275],
      [17.625, 275],
      [17.625, 300],
      [0, 300],
      [0, 275]],

      [[17.625, 275],
      [35.25, 275],
      [35.25, 300],
      [17.625, 300],
      [17.625, 275]],

      [[62.875, 275],
      [80.5, 275],
      [80.5, 300],
      [62.875, 300],
      [62.875, 275]],

      [[80.5, 275],
      [98.125, 275],
      [98.125, 300],
      [80.5, 300],
      [80.5, 275]],

      [[125.75, 275],
      [149.25, 275],
      [149.25, 300],
      [125.75, 300],
      [125.75, 275]],

      [[149.25, 275],
      [172.75, 275],
      [172.75, 300],
      [149.25, 300],
      [149.25, 275]],

      [[172.75, 275],
      [196.25, 275],
      [196.25, 300],
      [172.75, 300],
      [172.75, 275]],

      [[196.25, 275],
      [219.75, 275],
      [219.75, 300],
      [196.25, 300],
      [196.25, 275]],

      [[219.75, 275],
      [243.25, 275],
      [243.25, 300],
      [219.75, 300],
      [219.75, 275]],

      [[243.25, 275],
      [266.75, 275],
      [266.75, 300],
      [243.25, 300],
      [243.25, 275]],

      [[266.75, 275],
      [290.25, 275],
      [290.25, 300],
      [266.75, 300],
      [266.75, 275]],

      [[290.25, 275],
      [313.75, 275],
      [313.75, 300],
      [290.25, 300],
      [290.25, 275]],

      [[313.75, 275],
      [337.25, 275],
      [337.25, 300],
      [313.75, 300],
      [313.75, 275]],

      [[364.875, 275],
      [388.375, 275],
      [388.375, 300],
      [364.875, 300],
      [364.875, 275]],

      [[388.375, 275],
      [411.875, 275],
      [411.875, 300],
      [388.375, 300],
      [388.375, 275]],

      [[411.875, 275],
      [435.375, 275],
      [435.375, 300],
      [411.875, 300],
      [411.875, 275]],

      [[435.375, 275],
      [458.875, 275],
      [458.875, 300],
      [435.375, 300],
      [435.375, 275]],

      [[458.875, 275],
      [482.375, 275],
      [482.375, 300],
      [458.875, 300],
      [458.875, 275]],
    ],
    solidWhiteLineBeigeFill
    ).bindPopup("Phase Three");


    var phaseFour = L.polygon([
      [[0, 300],
      [17.625, 300],
      [17.625, 325],
      [0, 325],
      [0, 300]],

      [[17.625, 300],
      [35.25, 300],
      [35.25, 325],
      [17.625, 325],
      [17.625, 300]],

      [[62.875, 300],
      [98.125, 300],
      [98.125, 325],
      [62.875, 325],
      [62.875, 300]],

      [[125.75, 300],
      [149.25, 300],
      [149.25, 325],
      [125.75, 325],
      [125.75, 300]],

      [[149.25, 300],
      [172.75, 300],
      [172.75, 325],
      [149.25, 325],
      [149.25, 300]],

      [[172.75, 300],
      [196.25, 300],
      [196.25, 325],
      [172.75, 325],
      [172.75, 300]],

      [[196.25, 300],
      [219.75, 300],
      [219.75, 325],
      [196.25, 325],
      [196.25, 300]],

      [[219.75, 300],
      [243.25, 300],
      [243.25, 325],
      [219.75, 325],
      [219.75, 300]],

      [[243.25, 300],
      [266.75, 300],
      [266.75, 325],
      [243.25, 325],
      [243.25, 300]],

      [[266.75, 300],
      [290.25, 300],
      [290.25, 325],
      [266.75, 325],
      [266.75, 300]],

      [[290.25, 300],
      [313.75, 300],
      [313.75, 325],
      [290.25, 325],
      [290.25, 300]],

      [[313.75, 300],
      [337.25, 300],
      [337.25, 325],
      [313.75, 325],
      [313.75, 300]],

      [[364.875, 300],
      [388.375, 300],
      [388.375, 325],
      [364.875, 325],
      [364.875, 300]],

      [[388.375, 300],
      [411.875, 300],
      [411.875, 325],
      [388.375, 325],
      [388.375, 300]],

      [[411.875, 300],
      [435.375, 300],
      [435.375, 325],
      [411.875, 325],
      [411.875, 300]],
      
      [[435.375, 300],
      [458.875, 300],
      [458.875, 325],
      [435.375, 325],
      [435.375, 300]],

      [[492.375, 300],
      [515.875, 300],
      [515.875, 350],
      [492.375, 350],
      [492.375, 300]],

      [[492.375, 350],
      [515.875, 350],
      [515.875, 375],
      [492.375, 375],
      [492.375, 350]],




      [[0,  350],
      [26.99264705882353, 350],
      [26.99264705882353, 375],
      [0, 375],
      [0, 350]],
      
      [[26.99264705882353, 350],
      [53.98529411764706, 350],
      [53.98529411764706, 375],
      [26.99264705882353, 375],
      [26.99264705882353, 350]],
      
      [[53.98529411764706, 350],
      [80.97794117647059, 350],
      [80.97794117647059, 375],
      [53.98529411764706, 375],
      [53.98529411764706, 350]],

      [[80.97794117647059, 350],
      [107.9705882352941, 350],
      [107.9705882352941, 375],
      [80.97794117647059, 375],
      [80.97794117647059, 350]],

      [[107.9705882352941, 350],
      [134.9632352941177, 350],
      [134.9632352941177, 375],
      [107.9705882352941, 375],
      [107.9705882352941, 350]],

      [[134.9632352941177, 350],
      [161.9558823529412, 350],
      [161.9558823529412, 375],
      [134.9632352941177, 375],
      [134.9632352941177, 350]],

      [[161.9558823529412, 350],
      [188.9485294117648, 350],
      [188.9485294117648, 375],
      [161.9558823529412, 375],
      [161.9558823529412, 350]],

      [[188.9485294117648, 350],
      [215.9411764705883, 350],
      [215.9411764705883, 375],
      [188.9485294117648, 375],
      [188.9485294117648, 350]],

      [[215.9411764705883, 350],
      [242.9338235294118, 350],
      [242.9338235294118, 375],
      [215.9411764705883, 375],
      [215.9411764705883, 350]],

      [[242.9338235294118, 350],
      [269.9264705882354, 350],
      [269.9264705882354, 375],
      [242.9338235294118, 375],
      [242.9338235294118, 350]],

      [[269.9264705882354, 350],
      [296.9191176470589, 350],
      [296.9191176470589, 375],
      [269.9264705882354, 375],
      [269.9264705882354, 350]],

      [[296.9191176470589, 350],
      [323.9117647058824, 350],
      [323.9117647058824, 375],
      [296.9191176470589, 375],
      [296.9191176470589, 350]],

      [[323.9117647058824, 350],
      [350.9044117647059, 350],
      [350.9044117647059, 375],
      [323.9117647058824, 375],
      [323.9117647058824, 350]],

      [[350.9044117647059, 350],
      [377.8970588235295, 350],
      [377.8970588235295, 375],
      [350.9044117647059, 375],
      [350.9044117647059, 350]],

      [[377.8970588235295, 350],
      [404.889705882353, 350],
      [404.889705882353, 375],
      [377.8970588235295, 375],
      [377.8970588235295, 350]],

      [[404.889705882353, 350],
      [431.8823529411766, 350],
      [431.8823529411766, 375],
      [404.889705882353, 375],
      [404.889705882353, 350]],

      [[431.8823529411766, 350],
      [458.8750000000001, 350],
      [458.8750000000001, 375],
      [431.8823529411766, 375],
      [431.8823529411766, 350]],
    ],
    solidWhiteLineBeigeFill
    ).bindPopup("Phase Four");
*/
    
    var L1 = L.layerGroup([pathPhaseOne, pathPhaseTwo, pathPhaseThree, pathPhaseFour, entrancePhaseOne, entrancePhaseTwo, entrancePhaseTwo_2, entrancePhaseThree, entrancePhaseFour, marketPhaseLabel, legendLabel]);
    
    var baseMaps = {
      "Level 1": L1,
    };

    var map = L.map('map', {
        crs: L.CRS.Simple,
        maxZoom: 2.5,
        minZoom: 0.6,
        zoomSnap: 0.25,
        zoomDelta: 0.25,
        layers: [L1],
        zoomControl: false,
        center: [202, 200],
        zoom: 0.6,
        attributionControl: false
    });

    map.setMaxBounds(map.getBounds());
    
    map.addControl(new L.Control.Fullscreen({
      position: 'topright'
    }));

    L.control.zoom({
      position: 'topright'
    }).addTo(map);

    var stair = L.layerGroup([stair["P2-stair"], stair["P3-stair"], stair["P4-stair"]]);
    stair.addTo(map);

    var freezer = L.layerGroup([freezer["P4-freezer"]]);
    freezer.addTo(map);

    var phaseOne = L.layerGroup([polygons["P1-1"], polygons["P1-2"], polygons["P1-3"], polygons["P1-4"], polygons["P1-5"], polygons["P1-6"], polygons["P1-7"], polygons["P1-8"], polygons["P1-9"], polygons["P1-10"], polygons["P1-11"], polygons["P1-12"], polygons["P1-13"], polygons["P1-14"], polygons["P1-15"]]);
    var phaseTwo = L.layerGroup([polygons["P2-1"], polygons["P2-2"], polygons["P2-3"], polygons["P2-4"], polygons["P2-5"], polygons["P2-6"], polygons["P2-7"], polygons["P2-8"], polygons["P2-9"], polygons["P2-10"], polygons["P2-11"], polygons["P2-12"], polygons["P2-13"], polygons["P2-14"], polygons["P2-15"], polygons["P2-16"], polygons["P2-17"], polygons["P2-18"], polygons["P2-19"], polygons["P2-20"], polygons["P2-21"], polygons["P2-22"], polygons["P2-23"], polygons["P2-24"], polygons["P2-25"], polygons["P2-26"], polygons["P2-27"], polygons["P2-28"], polygons["P2-29"], polygons["P2-30"], polygons["P2-31"], polygons["P2-32"], polygons["P2-33"], polygons["P2-34"], polygons["P2-35"], polygons["P2-36"], polygons["P2-37"], polygons["P2-38"], polygons["P2-39"]]);
    var phaseThree = L.layerGroup([polygons["P3-1"], polygons["P3-2"], polygons["P3-3"], polygons["P3-4"], polygons["P3-5"], polygons["P3-6"], polygons["P3-7"], polygons["P3-8"], polygons["P3-9"], polygons["P3-10"], polygons["P3-11"], polygons["P3-12"], polygons["P3-13"], polygons["P3-14"], polygons["P3-15"], polygons["P3-16"], polygons["P3-17"], polygons["P3-18"], polygons["P3-19"], polygons["P3-20"], polygons["P3-21"], polygons["P3-22"], polygons["P3-23"], polygons["P3-24"], polygons["P3-25"], polygons["P3-26"], polygons["P3-27"], polygons["P3-28"], polygons["P3-29"], polygons["P3-30"], polygons["P3-31"], polygons["P3-32"], polygons["P3-33"], polygons["P3-34"], polygons["P3-35"]]);
    var phaseFour = L.layerGroup([polygons["P4-1"], polygons["P4-2"], polygons["P4-3"], polygons["P4-4"], polygons["P4-5"], polygons["P4-6"], polygons["P4-7"], polygons["P4-8"], polygons["P4-9"], polygons["P4-10"], polygons["P4-11"], polygons["P4-12"], polygons["P4-13"], polygons["P4-14"], polygons["P4-15"], polygons["P4-16"], polygons["P4-17"], polygons["P4-18"], polygons["P4-19"], polygons["P4-20"], polygons["P4-21"], polygons["P4-22"], polygons["P4-23"], polygons["P4-24"], polygons["P4-25"], polygons["P4-26"], polygons["P4-27"], polygons["P4-28"], polygons["P4-29"], polygons["P4-30"], polygons["P4-31"], polygons["P4-32"]]);
    
    phaseOne.addTo(map);
    phaseTwo.addTo(map);
    phaseThree.addTo(map);
    phaseFour.addTo(map);
    containerWall.addTo(map);

  var stalls = <?php echo json_encode($stalls); ?>;

  map.whenReady(function() {
    updatePolygonStyles(stalls);
  });

    document.getElementById('show-hide-canteen').addEventListener('click', function() {
      toggleCategory('Canteen');
    });
    document.getElementById('show-hide-clothing').addEventListener('click', function() {
      toggleCategory('Clothing');
    });
    document.getElementById('show-hide-coconut').addEventListener('click', function() {
      toggleCategory('Coconut');
    });
    document.getElementById('show-hide-condiments').addEventListener('click', function() {
      toggleCategory('Condiments');
    });
    document.getElementById('show-hide-fruit').addEventListener('click', function() {
      toggleCategory('Fruits');
    });
    document.getElementById('show-hide-grocery').addEventListener('click', function() {
      toggleCategory('Grocery');
    });
    document.getElementById('show-hide-meat').addEventListener('click', function() {
      toggleCategory('Meat');
    });
    document.getElementById('show-hide-pharmacy').addEventListener('click', function() {
      toggleCategory('Pharmacy');
    });
    document.getElementById('show-hide-poultry').addEventListener('click', function() {
      toggleCategory('Poultry');
    });
    document.getElementById('show-hide-rice').addEventListener('click', function() {
      toggleCategory('Rice');
    });
    document.getElementById('show-hide-sarisari').addEventListener('click', function() {
      toggleCategory('SariSari');
      togglePolygons(null, 'SariSari');
    });
    document.getElementById('show-hide-toys').addEventListener('click', function() {
      toggleCategory('Toys');
    });
    document.getElementById('show-hide-vegetable').addEventListener('click', function() {
      toggleCategory('Vegetables');
    });

    
    function toggleCategory(category) {
    for (var key in polygons) {
        if (polygons.hasOwnProperty(key)) {
            var polygon = polygons[key];
            var stallCategory = getCategoryByStall(key);

            if (stallCategory === category) {
                var currentOpacity = polygon.options.fillOpacity;
                if (currentOpacity === 0.3) {
                    polygon.setStyle({ fillOpacity: 1 });
                    var iconHTML = icons[stallCategory] || '';
                    polygon.bindTooltip('<div class="tooltip-content" style="text-align: center;">' +
                        iconHTML + 
                        '<p class="stall-label phase-legend m-0 p-0">' + key + '</p>' +
                        '</div>', 
                        { 
                          permanent: true, 
                          direction: 'center', 
                          className: 'tooltipclass' 
                        });
                } else {
                    polygon.setStyle({ fillOpacity: 0.3 });
                    polygon.unbindTooltip();
                }
            }
        }
    }
}

    function getCategoryByStall(stall) {
      for (var i = 0; i < stalls.length; i++) {
        if (stalls[i].occupiedStall === stall) {
          return stalls[i].stallCategory;
        }
      }
      return null;
    }


    

    function updatePolygonStyles(stalls) {
    const occupiedStallSet = new Set(stalls.map(stall => stall.occupiedStall));

    stalls.forEach(function(stall) {
        var occupiedStall = stall.occupiedStall;
        var stallCategory = stall.stallCategory;

        if (polygons.hasOwnProperty(occupiedStall)) {
            var polygon = polygons[occupiedStall];
            var iconHTML = icons[stallCategory] || '';

            polygon.setStyle(occupied);
            polygon.bindTooltip(
                '<div class="tooltip-content" style="text-align: center;">' +
                iconHTML + 
                '<p class="stall-label phase-legend m-0 p-0">' + occupiedStall + '</p>' +
                '</div>', 
                { 
                  permanent: true, 
                  direction: 'center', 
                  className: 'tooltipclass' 
                }
            );
        }
    });

    for (var key in polygons) {
        if (polygons.hasOwnProperty(key)) {
            var polygon = polygons[key];
            if (!occupiedStallSet.has(key)) {
                polygon.setStyle(vacant);
                polygon.bindTooltip(
                    '<div class="tooltip-content" style="text-align: center;">' +
                    '<p class="stall-label phase-legend m-0 p-0">' + key + '</p>' +
                    '</div>', 
                    { 
                      permanent: true, 
                      direction: 'center', 
                      className: 'tooltipclass' 
                    }
                );
            }
        }
    }
}

    function adjustTitleFontSize() {
      var zoomLevel = map.getZoom();
      var baseFontSize = 2.25;
      var newFontSize = baseFontSize * (zoomLevel + 1);
    
      document.querySelectorAll('.title-label').forEach(function(label) {
        label.style.fontSize = newFontSize + 'rem';
      });
    }

    map.on('zoomend', adjustTitleFontSize);
    adjustTitleFontSize();

    function adjustLegendTitleFontSize() {
      var zoomLevel = map.getZoom();
      var baseFontSize = 1.5;
      var newFontSize = baseFontSize * (zoomLevel + 1);
    
      document.querySelectorAll('.legend-title-label').forEach(function(label) {
        label.style.fontSize = newFontSize + 'rem';
      });
    }

    map.on('zoomend', adjustLegendTitleFontSize);
    adjustLegendTitleFontSize();

    function adjustLabelFontSize() {
      var zoomLevel = map.getZoom();
      var baseFontSize = 1;
      var newFontSize = baseFontSize * (zoomLevel + 1);
    
      document.querySelectorAll('.phase-label').forEach(function(label) {
        label.style.fontSize = newFontSize + 'rem';
      });
    }

    map.on('zoomend', adjustLabelFontSize);
    adjustLabelFontSize();

    function adjustStallLabelFontSize() {
      var zoomLevel = map.getZoom();
      var baseFontSize = .4;
      var newFontSize = baseFontSize * (zoomLevel + 1);
    
      document.querySelectorAll('.stall-label').forEach(function(label) {
        label.style.fontSize = newFontSize + 'rem';
      });
    }

    map.on('zoomend', adjustStallLabelFontSize);
    adjustStallLabelFontSize();

    function adjustTextFontSize() {
      var zoomLevel = map.getZoom();
      var baseFontSize = .5;
      var newFontSize = baseFontSize * (zoomLevel + 1);
      document.querySelectorAll('.entry-text').forEach(function(label) {
        label.style.fontSize = newFontSize + 'rem';
      });
    }

    map.on('zoomend', adjustTextFontSize);
    adjustTextFontSize();

    function adjustArrowFontSize() {
      var zoomLevel = map.getZoom();
      var baseFontSize = 1.25;
      var newFontSize = baseFontSize * (zoomLevel + 1);
      document.querySelectorAll('.entrance-arrow').forEach(function(label) {
        label.style.fontSize = newFontSize + 'rem';
      });
    }

    map.on('zoomend', adjustArrowFontSize);
    adjustArrowFontSize();

    function simulateZoomClicks() {
      var zoomInButton = document.querySelector('.leaflet-control-zoom-in');
      var zoomOutButton = document.querySelector('.leaflet-control-zoom-out');
    
      if (zoomInButton && zoomOutButton) {
        zoomInButton.click();
        setTimeout(() => {
          setTimeout(() => {
            zoomOutButton.click();
            document.getElementById('map').style.visibility = 'visible';
          }, 500);
        }, 100);
      }
    }
    
    map.whenReady(function() {
      simulateZoomClicks();
    });
  </script>

  <script src="bootstrap-5.3.2-dist/js/bootstrap.bundle.js "></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="js/guide-map.js"></script>

  <script src="js/jquery.min.js"></script>
  <script src="js/popper.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/main.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
        var menu = document.getElementById("stallCategoryMenu");
        if (menu) {
            menu.classList.toggle("show");
        }
    });
</script>
</body>
</html>