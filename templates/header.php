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
            width: 100%;
            box-sizing: border-box;
            border-bottom: 1px solid grey;
            box-shadow: 0px 2px 4px grey;
            display: flex;
            justify-content: space-between;
        }
        
        .topnavA {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 10px;
        }
        
        .logo-container img {
            height: 100px;
        }
        
        .menu-icon {
            display: none;
            font-size: 24px;
            cursor: pointer;
        }
        
        .menu-list {
            display: flex;
            align-items: center;
            padding: 0px;
            list-style-type: none; /* Remove bullets */
            margin-right: 480px;
        }
        
        .menu-list li {
            margin-right: 20px;
            display: relative;
            border: none; /* Remove the border-width property */
            padding: 5px; /* Add padding for the border effect */
            box-sizing: border-box;
        }
        
        .menu-list li a {
            text-decoration: none; /* Remove the underline */
            border-bottom: 2px solid transparent; /* Add a transparent bottom border */
            transition: border-bottom-color 0.3s ease; /* Add transition effect */
        }
        
        .menu-list li a:hover {
            border-bottom-color: #000; /* Change the border color on hover */
        }
        
        .menu-list li a.selected {
            border-bottom-color: #000;
            background-color: grey;
        }
        
        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(-100%);
            }
        }
        
        /* Media query for smaller screens */
    @media only screen and (max-width: 768px) {
      /* Styles for smaller screens */
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
      
    .menu-list li a.selected {
        border-bottom-color: #000;
        background-color: grey;
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
    }

    </style>

    <header class="header">
        <div class="topnavA">    
            <div class="logo-container">
                <a href="/" target="">
                    <img src="logos/Logo.jpg" alt="Logo"/>
                </a>
            </div>
            <div class="menu-icon">&#9776;</div>
            <ul class="menu-list" style="font-weight:bold; font-size:24px;">
                <li><a href="/" id="homes">HOME</a></li>
                <!-- <li><a href="/products" id="product">PRODUCTS</a></li> 
                <li><a href="/services" id="service">SERVICES</a></li>-->
                <li><a href="/book" id="booking">BOOK</a></li>
                <li><a href="/contacts" id="contacts">CONTACTS</a></li>
                <li><a href="/about" id="abouts">ABOUT</a></li> 
            </ul>

        </div>

    </header>
    <!-- Your page content goes here -->
    
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