<style>
    .header {
        background-color: #f8f8f8;
        padding: 0;
        position: fixed;
        top: 0;
        left: 10;
        right: 10;
        z-index: 9999;
        height: 120px;
        width: 99%;
        box-sizing: border-box;
        border-bottom: 1px solid grey;
        box-shadow: 0px 2px 4px grey;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        
    }

    .topnavA {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 10px;
        width: 98%;
    }

    .logo-container img {
        height: 100px;
    }

    .menu-block {
       /*display: flex;*/
        align-items: center;
        width:98%;
        padding:1px;
    }

    .menu-list {
        display: flex;
        align-items: center;
        padding: 0px;
        list-style-type: none;
        margin-right: 480px;
    }

    .menu-list li {
        display: inline-block;
        margin-right: 5px;
        padding: 1px;
        box-sizing: border-box;
        height: 24px;
    }

    .menu-list li a {
        text-decoration: none;
        border-bottom: 2px solid transparent;
        transition: border-bottom-color 0.3s ease;
    }

    .menu-list li a.selected {
        border-bottom-color: #000;
        background-color: grey;
    }

    #welcoming {
        padding-right: auto;
        font-size: 18px;
        margin-right: 400px;
    }
    
    .menu-icon {
        display: none; /* Hide the menu icon by default on larger screens */
    }
    .oval {
      display: inline-block;
      border-radius: 50px;
      border: 2px solid #000;
      padding: 5px 10px;
      
    }
    
    .oval a {
      text-decoration: none;
      color: #000;
    }
    
    @media only screen and (max-width: 1366px) {
      /* Styles for smaller screens of width 768*/
      .topnavA {
        flex-direction: column;
        align-items: flex-start;
      }
    
      .menu-list {
        display: none;
        flex-direction: column;
        align-items: flex-start;
        margin-top: 20px;
        padding: 0;
        background-color: lightgrey;
      }
    
      .menu-list.active {
        display: block;
      }
    
      .menu-list li {
        margin: 5px 0;
      }
    
      .menu-icon {
        display: flex;
        cursor: pointer;
        margin-right: 10px;
        font-size: 24px;
      }
      
      #time {
          display:none;
      }
    }
    }
</style>

<?php
    // Initialize $export_allowed to false by default
    $show_allowed = false;
    
    // Check if the session username is "Tim" or "Millie"
    if (isset($_SESSION['username']) && ($_SESSION['username'] == $_ENV['ADMIN1'] || $_SESSION['username'] == $_ENV['ADMIN2'])) {
        // User is authorized to export tables
        $show_allowed = true;
    }
    
    $date = date('Y-m-d H:i:s',strtotime('+3 hours') );
?>

<header class="header">
    <div class="topnavA">
        <div class="logo-container">
            <a href="/demo/admins" target="">
                <img src="logos/Logo.jpg" alt="Logo" />
            </a>
        </div>
        <div class="menu-icon">&#9776;</div>
        <div class="menu-block">
            <ul class="menu-list" style="font-weight:bold; font-size:24px;">
                <li><a href="/demo/admins" id="adminA">HOME</a></li>
                <li><a href="/demo/pay" id="payA">PAYMENTS</a></li>
                <li><a href="/demo/rewards" id="rewardA">REWARDS</a></li>
                <li><a href="/demo/inventory" id="inventoryYA">INVENTORY</a></li> 
                <li><a href="/demo/bookingsmgt" id="bookingsA">BOOKINGS</a></li>

                <li><a href="/demo/expenses" id="expensesA">EXPENSES</a></li>
                 <li><a href="/demo/commissions" id="payCommissions">COMMISSIONS</a></li> 
                <li><a href="/demo/performance" id="performance">PERFORMANCE</a></li>
                <li><a href="/demo/marketing" id="marketing">MARKETING</a></li>

                <li> <div id="time" onload=updateTime() style="width:80px; font-size: 12px; border: solid lightgrey; padding:1px;" </div></li>
            </ul>
            <div id="welcoming"> Welcome,
              <span class="oval"><a href="profile"><?php echo $_SESSION['username']; ?></a></span> 
              <a href="/demo/logout">Logout</a>
            </div>
            

        </div>
        
    </div>
    <br>
    <br>
</header>


<script>
    const menuIcon = document.querySelector('.menu-icon');
    const menuList = document.querySelector('.menu-list');

    menuIcon.addEventListener('click', () => {
        menuList.classList.toggle('active');
    });

    // Get the current page URL
    const currentPageUrl = window.location.pathname;

    // Find the corresponding menu item and add the 'selected' class
    const menuItems = document.querySelectorAll('.menu-list li a');
    menuItems.forEach((menuItem) => {
        const menuItemUrl = new URL(menuItem.href).pathname;
        if (menuItemUrl === currentPageUrl) {
            menuItem.classList.add('selected');
        } else {
            menuItem.classList.remove('selected');
        }
    });
    </script>
        
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script type="text/javascript">
        var timestamp = new Date();
        timestamp.setHours(timestamp.getHours());
        
        function formatDate(date) {
            var options = { day: 'numeric', month: 'long', year: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric' };
            var formattedDate = date.toLocaleString(undefined, options);
            return formattedDate.replace(/\b(\d{1,2})\b/, '$1' + getDaySuffix(date.getDate()));
        }
        
        function getDaySuffix(day) {
            if (day >= 11 && day <= 13) {
                return 'th';
            }
            switch (day % 10) {
                case 1: return 'st';
                case 2: return 'nd';
                case 3: return 'rd';
                default: return 'th';
            }
        }
        
        function updateTime() {
            var formattedTime = formatDate(timestamp);
            $('#time').html(formattedTime);
            timestamp.setTime(timestamp.getTime() + 1000);
        }
        
        $(function() {
            setInterval(updateTime, 1000);
        });
    </script>