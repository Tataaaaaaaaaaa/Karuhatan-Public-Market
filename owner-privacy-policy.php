<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
if ($conn->connect_error) {
  echo json_encode(array("success" => false, "message" => "Connection failed: " . $conn->connect_error));
} else {
  if (isset($_SESSION['userId'])) {
    $userID = $_SESSION['userId'];

    $stmt = $conn->prepare("SELECT * FROM user_registration WHERE user_registration_id = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      if ($row["userRole"] === 'owner') {

      $stmt_initial_load = $conn->prepare("SELECT emailAddress FROM user_registration WHERE user_registration_id = ?");
      $stmt_initial_load->bind_param("i", $userID);
      $stmt_initial_load->execute();
      $result_initial_load = $stmt_initial_load->get_result();

      if ($result_initial_load->num_rows > 0) {
        $row_initial_load = $result_initial_load->fetch_assoc();
        $emailAddress = $row_initial_load['emailAddress'];
      }

      $stmt_initial_load->close();
          
      } else {
        header("Location: log-in.html");
        exit();
      }
    } else {
      header("Location: log-in.html");
      exit();
    }
  } else {
    header("Location: log-in.html");
    exit();
  }
}
?>

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

    <link rel="stylesheet" href="css/owner-privacy-policy-style.css">
    <link rel="stylesheet" href="bootstrap-5.3.2-dist/css/bootstrap.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg-tenant navbar-dark bg-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="owner-dashboard.php">
              <img src="assets/img/Karuhatan Public Market Logo.png" alt="Karuhatan Public Market Logo" class="img-fluid" id="kpmLogo"/>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="navbarNavDropdown">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-text" href="owner-dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-text" href="owner-report.php">Reports</a>
                    </li>
                    <li class="nav-item dropdown">
                      <div class="m-0 p-0" style="position: relative">
                        <a class="nav-link nav-text dropdown-toggle" href="#" id="navbarDropdownUsersLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">Users</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownUsersLink">
                            <li>
                              <a class="dropdown-item nav-text" href="owner-users-tenants.php">Tenants</a>
                            </li>
                            <li>
                              <a class="dropdown-item nav-text" href="owner-users-collectors.php">Collectors</a>
                            </li>
                        </ul>
                      </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-text" href="owner-stalls.php">Stalls</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link nav-text" href="owner-applications.php">Applications</a>
                  </li>
                    <li class="nav-item" id="hidden-nav">
                      <a class="nav-link nav-text" href="owner-notifications.php">Notifications</a>
                    </li>
                    <li class="nav-item dropdown" id="hidden-nav">
                      <a class="nav-link nav-text dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $emailAddress; ?>&nbsp;</a>
                      <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                          <li>
                            <a class="dropdown-item nav-text" href="owner-profile.php">Profile</a>
                          </li>
                          <li>
                            <a class="dropdown-item nav-text" href="owner-change-password.php">Change Password</a>
                          </li>
                          <li>
                            <a class="dropdown-item nav-text" href="php/logout.php">Log Out</a>
                          </li>
                      </ul>
                    </li>
                </ul>
                <div class="d-flex align-items-center justify-content-center" id="hidden-side-nav">
                    <div class="me-5 nav-text">
                        <a href="owner-notifications.php">
                          <i class="fa-solid fa-bell" style="color: #283891"></i>
                        </a>
                    </div>
                    <div class="dropdown ms-6">
                        <button class="btn nav-text dropdown-toggle" style="border: none; padding-left: 5px" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $emailAddress; ?></button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item nav-text" href="owner-profile.php">Profile</a></li>
                            <li><a class="dropdown-item nav-text" href="owner-change-password.php">Change Password</a></li>
                            <li><a class="dropdown-item nav-text" href="php/logout.php">Log Out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div lc-helper="background" class="container-fluid py-0 d-flex justify-content-center" style="background-image: url('assets/img/about-us-bg.png'); background-position: center; background-size:cover; background-repeat:no-repeat">
      <div class="container-fluid px-4 py-0 my-4 col-8 text-center">
          <h2 class="display-3 switzer-bold-text-white">Privacy Policy</h2>
      </div>
    </div>

    <main ng-app="ng-toc-demo" ng-cloak class="container-fluid m-0 p-0" style="background-color: white">
      <div class="row col-12 m-0 p-0">
          <aside class="col-3 d-flex justify-content-center">
              <div class="toc mx-2">
                <ng-toc-list class="display-xsm"/>
              </div>
          </aside>
          <section class="content col-9" ng-toc-target="h1, h2">

              <h1>1. Introduction</h1>
              <p>Welcome to Karuhatan Public Market.</p>
              <p>Karuhatan Public Market (“us”, “we”, or “our”) operates www.KaruhatanPublicMarket.com (hereinafter referred to as “Service”).</p>
              <p>Our Privacy Policy governs your visit to www.KaruhatanPublicMarket.com, and explains how we collect, safeguard and disclose information that results from your use of our Service.</p>
              <p>We use your data to provide and improve Service. By using Service, you agree to the collection and use of information in accordance with this policy. Unless otherwise defined in this Privacy Policy, the terms used in this Privacy Policy have the same meanings as in our Terms and Conditions.</p>
              <p>Our Terms and Conditions (“Terms”) govern all use of our Service and together with the Privacy Policy constitutes your agreement with us (“agreement”).</p>

              <h1>2. Definitions</h1>
              <p><span class="bold">SERVICE </span>means the www.KaruhatanPublicMarket.com website operated by Karuhatan Public Market.</p>
              <p><span class="bold">PERSONAL DATA </span>means data about a living individual who can be identified from those data (or from those and other information either in our possession or likely to come into our possession).</p>
              <p><span class="bold">USAGE DATA </span>is data collected automatically either generated by the use of Service or from Service infrastructure itself (for example, the duration of a page visit).</p>
              <p><span class="bold">COOKIES </span>are small files stored on your device (computer or mobile device).</p>
              <p><span class="bold">DATA CONTROLLER </span>means a natural or legal person who (either alone or jointly or in common with other persons) determines the purposes for which and the manner in which any personal data are, or are to be, processed. For the purpose of this Privacy Policy, we are a Data Controller of your data.</p>
              <p><span class="bold">DATA PROCESSORS (OR SERVICE PROVIDERS) </span>means any natural or legal person who processes the data on behalf of the Data Controller. We may use the services of various Service Providers in order to process your data more effectively.</p>
              <p><span class="bold">DATA SUBJECT </span>is any living individual who is the subject of Personal Data.</p>
              <p><span class="bold">THE USER </span>is the individual using our Service. The User corresponds to the Data Subject, who is the subject of Personal Data.</p>

              <h1>3. Information Collection and Use</h1>
              <p>We collect several different types of information for various purposes to provide and improve our Service to you.</p>

              <h1>4. Types of Data Collected</h1>
              <p class="bold">Personal Data</p>
              <p>While using our Service, we may ask you to provide us with certain personally identifiable information that can be used to contact or identify you (“Personal Data”). Personally identifiable information may include, but is not limited to:</p>
              <ul>
                <li>0.1. Email address</li>
                <li>0.2. First name and last name</li>
                <li>0.3. Phone number</li>
                <li>0.4. Address, Country, State, Province, ZIP/Postal code, City</li>
                <li>0.5. Cookies and Usage Data</li>
              </ul> 
              <p>We may use your Personal Data to contact you with newsletters, marketing or promotional materials and other information that may be of interest to you. You may opt out of receiving any, or all, of these communications from us by following the unsubscribe link.</p>

              <p class="bold">Usage Data</p>
              <p>We may also collect information that your browser sends whenever you visit our Service or when you access Service by or through any device (“<span class="bold">Usage Data</span>”).</p>
              <p>This Usage Data may include information such as your computer’s Internet Protocol address (e.g. IP address), browser type, browser version, the pages of our Service that you visit, the time and date of your visit, the time spent on those pages, unique device identifiers and other diagnostic data.</p>
              <p>When you access Service with a device, this Usage Data may include information such as the type of device you use, your device unique ID, the IP address of your device, your device operating system, the type of Internet browser you use, unique device identifiers and other diagnostic data.</p>

              <p class="bold">Location Data</p>
              <p>We may use and store information about your location if you give us permission to do so (“<span class="bold">Location Data</span>”). We use this data to provide features of our Service, to improve and customize our Service.</p>
              <p>You can enable or disable location services when you use our Service at any time by way of your device settings.</p>

              <p class="bold">Tracking Cookies Data</p>
              <p>We use cookies and similar tracking technologies to track the activity on our Service and we hold certain information.</p>
              <p>Cookies are files with a small amount of data which may include an anonymous unique identifier. Cookies are sent to your browser from a website and stored on your device. Other tracking technologies are also used such as beacons, tags and scripts to collect and track information and to improve and analyze our Service.</p>
              <p>You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our Service.</p>
              <p>Examples of Cookies we use:</p>
              <ul>
                <li>0.1. <span class="bold">Session Cookies:</span> We use Session Cookies to operate our Service.</li>
                <li>0.2. <span class="bold">Preference Cookies:</span> We use Preference Cookies to remember your preferences and various settings.</li>
                <li>0.3. <span class="bold">Security Cookies:</span> We use Security Cookies for security purposes.</li>
                <li>0.4. <span class="bold">Advertising Cookies:</span> Advertising Cookies are used to serve you with advertisements that may be relevant to you and your interests.</li>
              </ul>

              <p class="bold">Other Data</p>
              <p>While using our Service, we may also collect the following information: sex, age, date of birth, place of birth, passport details, citizenship, registration at place of residence and actual address, telephone number (work, mobile), details of documents on education, qualification, professional training, employment agreements, NDA agreements, information on bonuses and compensation, information on marital status, family members, social security (or other taxpayer identification) number, office location and other data.</p>

              <h1>5. Use of Data</h1>
              <p>YKaruhatan Public Market uses the collected data for various purposes:</p>
              <ul>
                  <li>0.1. to provide and maintain our Service;</li>
                  <li>0.2. to notify you about changes to our Service;</li>
                  <li>0.3. to allow you to participate in interactive features of our Service when you choose to do so;</li>
                  <li>0.4. to provide customer support</li>
                  <li>0.5. to gather analysis or valuable information so that we can improve our Service;</li>
                  <li>0.6. to monitor the usage of our Service;</li>
                  <li>0.7. to detect, prevent and address technical issues;</li>
                  <li>0.8. to fulfil any other purpose for which you provide it;</li>
                  <li>0.9. to carry out our obligations and enforce our rights arising from any contracts entered into between you and us, including for billing and collection;</li>
                  <li>0.10. to provide you with notices about your account and/or subscription, including expiration and renewal notices, email-instructions, etc.;</li>
                  <li>0.11. to provide you with news, special offers and general information about other goods, services and events which we offer that are similar to those that you have already purchased or enquired about unless you have opted not to receive such information;</li>
                  <li>0.12. in any other way we may describe when you provide the information;</li>
                  <li>0.13. for any other purpose with your consent.</li>
              </ul>

              <h1>6. Retention of Data</h1>
              <p>We will retain your Personal Data only for as long as is necessary for the purposes set out in this Privacy Policy. We will retain and use your Personal Data to the extent necessary to comply with our legal obligations (for example, if we are required to retain your data to comply with applicable laws), resolve disputes, and enforce our legal agreements and policies.</p>
              <p>We will also retain Usage Data for internal analysis purposes. Usage Data is generally retained for a shorter period, except when this data is used to strengthen the security or to improve the functionality of our Service, or we are legally obligated to retain this data for longer time periods.</p>

              <h1>7. Transfer of Data</h1>
              <p>Your information, including Personal Data, may be transferred to – and maintained on – computers located outside of your state, province, country or other governmental jurisdiction where the data protection laws may differ from those of your jurisdiction.</p>
              <p>If you are located outside Philippines and choose to provide information to us, please note that we transfer the data, including Personal Data, to Philippines and process it there.</p>
              <p>Your consent to this Privacy Policy followed by your submission of such information represents your agreement to that transfer.</p>
              <p>Karuhatan Public Market will take all the steps reasonably necessary to ensure that your data is treated securely and in accordance with this Privacy Policy and no transfer of your Personal Data will take place to an organisation or a country unless there are adequate controls in place including the security of your data and other personal information.</p>

              <h1>8. Disclosure of Data</h1>
              <p>We may disclose personal information that we collect, or you provide:</p>
              <p>0.1. <span class="bold">Business Transaction.</span></p>
              <p>If we or our subsidiaries are involved in a merger, acquisition or asset sale, your Personal Data may be transferred.</p>
              <p>0.2. <span class="bold">Other cases. We may disclose your information also:</span></p>
              <ul>
                <li>0.2.1. to our subsidiaries and affiliates;</li>
                <li>0.2.2. to contractors, service providers, and other third parties we use to support our business;</li>
                <li>0.2.3. to fulfill the purpose for which you provide it;</li>
                <li>0.2.4. for the purpose of including your company’s logo on our website;</li>
                <li>0.2.5. for any other purpose disclosed by us when you provide the information;</li>
                <li>0.2.6. with your consent in any other cases;</li>
                <li>0.2.7. if we believe disclosure is necessary or appropriate to protect the rights, property, or safety of the Company, our customers, or others.</li>
              </ul>

              <h1>9. Security of Data</h1>
              <p>The security of your data is important to us but remember that no method of transmission over the Internet or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your Personal Data, we cannot guarantee its absolute security.</p>

              <h1>10. Your Data Protection Rights Under General Data Protection Regulation (GDPR)</h1>
              <p>If you are a resident of the European Union (EU) and European Economic Area (EEA), you have certain data protection rights, covered by GDPR.</p>
              <p>We aim to take reasonable steps to allow you to correct, amend, delete, or limit the use of your Personal Data.</p>
              <p>If you wish to be informed what Personal Data we hold about you and if you want it to be removed from our systems, please email us at <span class="bold">karuhatanmarketoffice@gmail.com.</span></p>
              <p>In certain circumstances, you have the following data protection rights:</p>
              <ul>
                <li>0.1. the right to access, update or to delete the information we have on you;</li>
                <li>0.2. the right of rectification. You have the right to have your information rectified if that information is inaccurate or incomplete;</li>
                <li>0.3. the right to object. You have the right to object to our processing of your Personal Data;</li>
                <li>0.4. the right of restriction. You have the right to request that we restrict the processing of your personal information;</li>
                <li>0.5. the right to data portability. You have the right to be provided with a copy of your Personal Data in a structured, machine-readable and commonly used format;</li>
                <li>0.6. the right to withdraw consent. You also have the right to withdraw your consent at any time where we rely on your consent to process your personal information;</li>
              </ul>

              <p>Please note that we may ask you to verify your identity before responding to such requests. Please note, we may not able to provide Service without some necessary data.</p>
              <p>You have the right to complain to a Data Protection Authority about our collection and use of your Personal Data. For more information, please contact your local data protection authority in the European Economic Area (EEA).</p>

              <h1>11. Your Data Protection Rights under the California Privacy Protection Act (CalOPPA)</h1>
              <p>CalOPPA is the first state law in the nation to require commercial websites and online services to post a privacy policy. The law’s reach stretches well beyond California to require a person or company in the United States (and conceivable the world) that operates websites collecting personally identifiable information from California consumers to post a conspicuous privacy policy on its website stating exactly the information being collected and those individuals with whom it is being shared, and to comply with this policy.</p>
              <p>According to CalOPPA we agree to the following:</p>
              <ul>
                <li>0.1. users can visit our site anonymously;</li>
                <li>0.2. our Privacy Policy link includes the word “Privacy”, and can easily be found on the home page of our website;</li>
                <li>0.3. users will be notified of any privacy policy changes on our Privacy Policy Page;</li>
                <li>0.4. users are able to change their personal information by emailing us at <span class="bold">karuhatanmarketoffice@gmail.com.</span></li>
              </ul>

              <p>Our Policy on “Do Not Track” Signals:</p>
              <p>We honor Do Not Track signals and do not track, plant cookies, or use advertising when a Do Not Track browser mechanism is in place. Do Not Track is a preference you can set in your web browser to inform websites that you do not want to be tracked.</p>
              <p>You can enable or disable Do Not Track by visiting the Preferences or Settings page of your web browser.</p>

              <h1>12. Your Data Protection Rights under the California Consumer Privacy Act (CCPA)</h1>
              <p>If you are a California resident, you are entitled to learn what data we collect about you, ask to delete your data and not to sell (share) it. To exercise your data protection rights, you can make certain requests and ask us:</p>
              <p class="bold">0.1. What personal information we have about you. If you make this request, we will return to you:</p>
              <ul>
                <li>0.0.1. The categories of personal information we have collected about you.</li>
                <li>0.0.2. The categories of sources from which we collect your personal information.</li>
                <li>0.0.3. The business or commercial purpose for collecting or selling your personal information.</li>
                <li>0.0.4. The categories of third parties with whom we share personal information.</li>
                <li>0.0.5. The specific pieces of personal information we have collected about you.</li>
                <li>0.0.6. A list of categories of personal information that we have sold, along with the category of any other company we sold it to. If we have not sold your personal information, we will inform you of that fact.</li>
                <li>0.0.7. A list of categories of personal information that we have disclosed for a business purpose, along with the category of any other company we shared it with.</li>
              </ul>

              <p>Please note, you are entitled to ask us to provide you with this information up to two times in a rolling twelve-month period. When you make this request, the information provided may be limited to the personal information we collected about you in the previous 12 months.</p>
              <p class="bold">0.2. To delete your personal information. If you make this request, we will delete the personal information we hold about you as of the date of your request from our records and direct any service providers to do the same. In some cases, deletion may be accomplished through de-identification of the information. If you choose to delete your personal information, you may not be able to use certain functions that require your personal information to operate.</p>
              <p class="bold">0.3. To stop selling your personal information. We don’t sell or rent your personal information to any third parties for any purpose. We do not sell your personal information for monetary consideration. However, under some circumstances, a transfer of personal information to a third party, or within our family of companies, without monetary consideration may be considered a “sale” under California law. You are the only owner of your Personal Data and can request disclosure or deletion at any time.</p>

              <p>If you submit a request to stop selling your personal information, we will stop making such transfers.</p>
              <p>Please note, if you ask us to delete or stop selling your data, it may impact your experience with us, and you may not be able to participate in certain programs or membership services which require the usage of your personal information to function. But in no circumstances, we will discriminate against you for exercising your rights.</p>
              <p>To exercise your California data protection rights described above, please send your request(s) by email: <span class="bold">karuhatanmarketoffice@gmail.com.</span></p>
              <p>Your data protection rights, described above, are covered by the CCPA, short for the California Consumer Privacy Act. To find out more, visit the official California Legislative Information website. The CCPA took effect on 01/01/2020.</p>

              <h1>13.  Service Providers</h1>
              <p>We may employ third party companies and individuals to facilitate our Service (“<span class="bold">Service Providers</span>”), provide Service on our behalf, perform Service-related services or assist us in analysing how our Service is used.</p>
              <p>These third parties have access to your Personal Data only to perform these tasks on our behalf and are obligated not to disclose or use it for any other purpose.</p>

              <h1>14. Analytics</h1>
              <p>We may use third-party Service Providers to monitor and analyze the use of our Service.</p>

              <h1>15. CI/CD tools</h1>
              <p>We may use third-party Service Providers to automate the development process of our Service.</p>

              <h1>16.  Behavioral Remarketing</h1>
              <p>We may use remarketing services to advertise on third party websites to you after you visited our Service. We and our third-party vendors use cookies to inform, optimise and serve ads based on your past visits to our Service.</p>

              <h1>17. Payments</h1>
              <p>We may provide paid products and/or services within Service. In that case, we use third-party services for payment processing (e.g. payment processors).</p>
              <p>We will not store or collect your payment card details. That information is provided directly to our third-party payment processors whose use of your personal information is governed by their Privacy Policy. These payment processors adhere to the standards set by PCI-DSS as managed by the PCI Security Standards Council, which is a joint effort of brands like Visa, Mastercard, American Express and Discover. PCI-DSS requirements help ensure the secure handling of payment information.</p>

              <h1>18. Links to Other Sites</h1>
              <p>Our Service may contain links to other sites that are not operated by us. If you click a third party link, you will be directed to that third party’s site. We strongly advise you to review the Privacy Policy of every site you visit.</p>
              <p>We have no control over and assume no responsibility for the content, privacy policies or practices of any third party sites or services.</p>
              <p>For example, the outlined privacy policy has been made using PolicyMaker.io, a free tool that helps create high-quality legal documents. PolicyMaker’s privacy policy generator is an easy-to-use tool for creating a privacy policy for blog, website, e-commerce store or mobile app.</p>

              <h1>19. Children’s Privacy</h1>
              <p>Our Services are not intended for use by children under the age of 18 (<span class="bold">“Child”</span> or <span class="bold">“Children”</span>).</p>
              <p>We do not knowingly collect personally identifiable information from Children under 18. If you become aware that a Child has provided us with Personal Data, please contact us. If we become aware that we have collected Personal Data from Children without verification of parental consent, we take steps to remove that information from our servers.</p>

              <h1>20. Changes to This Privacy Policy</h1>
              <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
              <p>We will let you know via email and/or a prominent notice on our Service, prior to the change becoming effective and update “effective date” at the top of this Privacy Policy.</p>
              <p>You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted on this page.</p>

              <h1>21. Contact Us</h1>
              <p>If you have any questions about this Privacy Policy, please contact us by email: <span class="bold">karuhatanmarketoffice@gmail.com.</span>.</p>

          </section>
      </div>
  </main>
  
    
  <footer class="text-center text-lg-start">
        <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
        </section>
      
        <section class="">
          <div class="container text-center text-md-start mt-5">
            <div class="row mt-3">
              <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                <a href="owner-dashboard.php">
                    <img src="../Developer things/assets/img/Karuhatan Public Market Logo.png" alt="Karuhatan Public Market Logo" class="img-fluid mb-3 footer-logo">
                </a>
                <p class="switzer-medium-text">Join us at the Karuhatan Public Market, where every stall offers quality goods and every visit is an opportunity to discover something new.</p>
              </div>
      
              <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                <a href="owner-report.php" style="text-decoration: none"><h6 class="footer-text mb-4">Reports</h6></a>
                <a href="owner-users-tenants.php" style="text-decoration: none"><h6 class="footer-text mb-4">Tenants</h6></a>
                <a href="owner-users-collectors.php" style="text-decoration: none"><h6 class="footer-text mb-4">Users</h6></a>
                <a href="owner-stalls.php" style="text-decoration: none"><h6 class="footer-text mb-4">Stalls</h6></a>
              </div>
      
              <div class="col-md-3 col-lg-2 col-xl-3 mx-auto mb-4">
                <a href="owner-applications.php" style="text-decoration: none"><h6 class="footer-text mb-4">Applications</h6></a>
                <a href="owner-notifications.php" style="text-decoration: none"><h6 class="footer-text mb-4">Notifications</h6></a>
                <a href="owner-privacy-policy.php" style="text-decoration: none"><h6 class="footer-text mb-4">Privacy Policy</h6></a>
                <a href="owner-terms-and-conditions.php" style="text-decoration: none"><h6 class="footer-text mb-4">Terms and Conditions</h6></a>
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
          <a class="switzer-semibold-text-white" style="text-decoration: none">Karuhatan Public Market</a>
        </div>
    </footer>

    <script src="bootstrap-5.3.2-dist/js/bootstrap.bundle.js "></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>

    <script>
    angular.module('ng-toc-demo', [], function() {

    })
    
    .directive('ngTocTarget', function() {
      return {
        link: function(scope, elem, attrs) {
          attrs.ngTocTarget = attrs.ngTocTarget || 'h1, h2, h3';
          scope.ngToc = [];
          var runningList = [];
          var targetElements = attrs.ngTocTarget.toLowerCase().replace(/ /g,'').split(',');
          var index = 0;
    
          angular.forEach(elem.children(), function(obj) {
            var el = angular.element(obj)[0];
            var elType = el.nodeName.toLowerCase();
    
            if (targetElements.indexOf(elType) > -1) {
              var add = {};
              add.el = elType;
              add.contents = el.innerText;
    
              var elementId = el.innerText.replace(/ /g,'_');
    
              var isMatchIndex = runningList.indexOf(add.contents);
              var countIndex = isMatchIndex+1;
              runningList.push(el.innerText);
              runningList.push(0);
    
              if (isMatchIndex > -1) {
                runningList[countIndex] = runningList[countIndex] +1;
              }
    
              if (runningList[countIndex] > 0) {
                angular.element(el).attr('id', elementId + '_' + runningList[countIndex]);
                add.id = elementId + '_' + runningList[countIndex];
              } else {
                angular.element(el).attr('id', elementId);
                add.id = elementId;
              }
    
              scope.ngToc.push(add);
              index++;
            }
          });
        }
      };
    })
    
    .directive('ngTocList', function() {
      return {
        template: "<ul><li ng-repeat='item in ngToc track by $index'><a ng-click='scrollTo(this)' href='#'>{{item.contents}}</a></li></ul>",
        link: function(scope, elem, attrs) {
          scope.scrollTo = function(loc) {
            var el = document.getElementById(loc.item.id);
                $('html, body').animate({
                  scrollTop: el.offsetTop
                }, 50);
          }
        }
      };
    });        
  </script>
</body>
</html>
