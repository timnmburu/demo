<style>
/*
    .header {
        background-color: #f8f8f8;
        padding: 0;
        position: fixed;
        top: 0;
        left: 0; 
        z-index: 9999;
        width: 220px; 
        height: 100%; 
        box-sizing: border-box;
        border-right: 1px solid grey; 
        box-shadow: 2px 0px 4px grey; 
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
*/
    
    .logo-container {
        top: 0;
        left:0;
        z-index: 2;
        height: 200px;
        width: 219px;
        position: fixed;
        align-items: center;
        justify-content: center;
    }
    
    .logo-container img {
        height: 100px;
        width:219px;
    }
    
    .topnavA {
        background-color: #f8f8f8;
        padding: 0;
        position: fixed;
        /*top: 100px;*/
        top:0;
        left: 0; 
        width: 220px; 
        height: 100%; 
        box-sizing: border-box;
        border-right: 1px solid grey; 
        box-shadow: 2px 0px 4px grey;
        z-index: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .menu-icon {
        display: none; /* Hide the menu icon by default on larger screens */
    }

    .menu-block {
        background-color:  lavender;
        padding: 0;
        position: fixed;
        top: 110px;
        left: 0; 
        z-index: 9999;
        width: 219px; 
        height: 100%; 
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .menu-list {
        background-color:  lavender;
        padding: 0;
        position: fixed;
        top: 110px;
        left: 0; 
        z-index: 9999;
        width: 219px; 
        box-sizing: border-box;

        flex-direction: column;
        justify-content: space-between; /*//////////////////////////////////*/
        align-items: center;
        padding: 0px;
        list-style-type: none;
    }

    .menu-list li {
        margin-right: 5px;
        padding: 5px;
        box-sizing: border-box;
        border-right: 1px solid grey; 
        box-shadow: 2px 0px 4px grey; 
        height: 50px;
    }

    .menu-list li a {
        text-decoration: none;
    }

    .menu-item {
        position: relative;
        padding: 0;
        display: flex;
        flex-direction: column;
        float: none;
        flex-grow: 1;
        transition: background-color 0.3s;
    }
    
    .menu-item:hover {
        background-color: lightblue;
    }
    
    .menu-item.clicked,
    .menu-item.clicked a.menu-link {
        background-color: grey;
    }
    
    .horizontalBar {
        background-color:  lavender;
        position: fixed;
        top: 0;
        left: 220px;
        right: 100px;
        z-index: 1;
        height: 55px;
        width: 99.99%;
        box-sizing: border-box;
        border-bottom: 1px solid grey;
        box-shadow: 0px 2px 4px grey;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .welcoming {
        background-color:  lavender;
        position: fixed;
        top: 0;
        right: 0;
        z-index: 1;
        height: 49px;
        width: 250px;
        box-sizing: border-box;
        justify-content: center;
        align-items:center;
    }
    
    .oval {
      display: inline-block;
      border-radius: 50px;
      border: 2px solid black;
      padding: 5px 10px;
    }
    
    .oval a {
      text-decoration: none;
      color: black;
    }
    
    .body {
        margin-left: 220px; 
        margin-top: 0px; 
        padding: 20px;
    }
    /*////////////////////////////////////////////////////////////////////////////////////*/
    @media only screen and (max-width: 1366px) {
      /* Styles for smaller screens of width 768*/
    .topnavA, .menu-block {
        display: none;
    }
      
    .horizontalBar {
        background-color:  lavender;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1;
        height: 100px;
        width: 99.99%;
        box-sizing: border-box;
        border-bottom: 1px solid grey;
        box-shadow: 0px 2px 4px grey;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .welcoming {
        background-color:  lavender;
        position: fixed;
        top: 25px;
        left: 110px;
        z-index: 1;
        height: 49px;
        width: 250px;
        box-sizing: border-box;
        justify-content: center;
        align-items:center;
    }
      
    .logo-container {
        height: 100px;
        width: 119px;
        top:0;
        left:0;
        z-index: 9999;
        position: fixed;
        align-items: center;
        justify-content: center;
    }
    
    .logo-container img {
        height: 99px;
        width:110px;
    }
    
    .body {
        margin-left: 0; 
        margin-top: 100px; 
        padding: 5px;
    }
      
    .menu-icon {
        display: flex;
        top:90px;
        left:0;
        cursor: pointer;
        margin-right: 10px;
        font-size: 24px;
        zoom: 200%;
    }
    
    .menu-list {
        display: none;
    }
    
    .menu-list.show {
        display: block;
    }
      
    /*  
    .show-menu .menu-list {
        display: block;
    }
    
      .menu-list.active {
        display: block;
      }
    
    
      .menu-list li {
        margin: 5px 0;
      }
      */
    
    }
</style>

<header class="header">
    <?php
        if(!isset($_SESSION['access']) || $_SESSION['access'] === false){
            $access = 0;
        } else {
            $access = 1;
        }
        
    ?>

    <div class="topnavA">
    </div>
    <div class="logo-container">
        <a href="admins" target="">
            <img src="logos/Logo.jpg" alt="Logo" />
        </a>
    </div>
        <div class="menu-icon">&#9776;</div>
        
        <div class="menu-block">
        </div>
            <ul class="menu-list" style=" font-weight:bold; font-size:24px;">
                <li class="menu-item " aria-haspopup="true">
                    <a href="admins" class="menu-link">
                        <i class="menu-bullet menu-bullet-dot">
                            <span></span>
                        </i>
                        <span class="menu-text">HOME</span>
                    </a>
                </li>
                <li class="menu-item " aria-haspopup="true">
                    <a href="pay" class="menu-link">
                        <i class="menu-bullet menu-bullet-dot">
                            <span></span>
                        </i>
                        <span class="menu-text">PAYMENTS</span>
                    </a>
                </li>
                <?php
                    if($access === 0){
                        echo '';
                    } else {
                        
                ?>
                <li class="menu-item " aria-haspopup="true">
                    <a href="rewards" class="menu-link">
                        <i class="menu-bullet menu-bullet-dot">
                            <span></span>
                        </i>
                        <span class="menu-text">REWARDS</span>
                    </a>
                </li>
                <li class="menu-item " aria-haspopup="true">
                    <a href="bookingsmgt" class="menu-link">
                        <i class="menu-bullet menu-bullet-dot">
                            <span></span>
                        </i>
                        <span class="menu-text">BOOKINGS</span>
                    </a>
                </li>
                <li class="menu-item " aria-haspopup="true">
                    <a href="inventory" class="menu-link">
                        <i class="menu-bullet menu-bullet-dot">
                            <span></span>
                        </i>
                        <span class="menu-text">INVENTORY</span>
                    </a>
                </li>
                <li class="menu-item " aria-haspopup="true">
                    <a href="expenses" class="menu-link">
                        <i class="menu-bullet menu-bullet-dot">
                            <span></span>
                        </i>
                        <span class="menu-text">EXPENSES</span>
                    </a>
                </li>
                <li class="menu-item " aria-haspopup="true">
                    <a href="staffing" class="menu-link">
                        <i class="menu-bullet menu-bullet-dot">
                            <span></span>
                        </i>
                        <span class="menu-text">STAFFING</span>
                    </a>
                </li>
                <li class="menu-item " aria-haspopup="true">
                    <a href="commissions" class="menu-link">
                        <i class="menu-bullet menu-bullet-dot">
                            <span></span>
                        </i>
                        <span class="menu-text">COMMISSIONS</span>
                    </a>
                </li>
                <li class="menu-item " aria-haspopup="true">
                    <a href="marketing" class="menu-link">
                        <i class="menu-bullet menu-bullet-dot">
                            <span></span>
                        </i>
                        <span class="menu-text">MARKETING</span>
                    </a>
                </li>
                <li class="menu-item " aria-haspopup="true">
                    <a href="withdraw" class="menu-link">
                        <i class="menu-bullet menu-bullet-dot">
                            <span></span>
                        </i>
                        <span class="menu-text">SEND MONEY</span>
                    </a>
                </li>
                <li class="menu-item " aria-haspopup="true">
                    <a href="performance" class="menu-link">
                        <i class="menu-bullet menu-bullet-dot">
                            <span></span>
                        </i>
                        <span class="menu-text">PERFORMANCE</span>
                    </a>
                </li>
                <?php
                    }
                ?>
                <li class="menu-item " aria-haspopup="true">
                    <a href="profile" class="menu-link">
                        <i class="menu-bullet menu-bullet-dot">
                            <span></span>
                        </i>
                        <span class="menu-text">MY ACCOUNT</span>
                    </a>
                </li>
                <li class="menu-item " aria-haspopup="true" >
                    <a href="/book?ready=yes" class="menu-link" style="color:red;">
                        <i class="menu-bullet menu-bullet-dot" >
                            <span></span>
                        </i>
                        <span  class="menu-text">BUY NOW</span>
                    </a>
                </li>
            </ul>
        
    
    <div class="horizontalBar"></div>
    
    <div class="welcoming"> Welcome,
      <span class="oval"><a href="profile"><?php if(isset($_SESSION['username'])) { echo $_SESSION['username']; } else { echo "User"; } ?></a></span> 
      <a href="logout">Logout</a>
    </div>
</header>


<script>
//Script for menu-icon
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.menu-icon').addEventListener('click', function() {
            document.querySelector('.menu-list').classList.toggle('show');;
        });
    });
    
//Script for changing color of selected menu-item
document.addEventListener("DOMContentLoaded", function () {
    const menuItems = document.querySelectorAll(".menu-item");

    menuItems.forEach(function (menuItem) {
        const menuLink = menuItem.querySelector(".menu-link");
        const menuItemUrl = menuLink.getAttribute("href");

        if (window.location.href.includes(menuItemUrl)) {
            menuItem.classList.add("clicked");
        }

        menuItem.addEventListener("click", function (event) {
            // Only prevent default behavior if the URL matches the menu item's link
            if (window.location.href.includes(menuItemUrl)) {
                event.preventDefault();
            }

            menuItems.forEach(function (item) {
                item.classList.remove("clicked");
            });

            this.classList.add("clicked");
        });
    });
});

    
    
</script>
