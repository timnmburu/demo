<style>
#mySidenavAdmin a {
    position: fixed;
    right: -80px;
    transition: 0.3s;
    height: 50px;
    width: 100px;
    text-decoration: none;
    font-size: 20px;
    color: white;
    border-radius: 0 5px 5px 0;
}

#mySidenavAdmin a:hover {
    right: 0;
}

#home {
    top: 150px;
    background-color: #000080; /* Navy */
}

#admin {
    top: 210px;
    background-color: #000080; /* Navy */
}

#pay {
    top: 270px;
    background-color: #000080; /* Navy */
}

#reward {
    top: 344px;
    background-color: #000080; /* Navy */
}
#inventoryY {
    top: 417px;
    background-color: #000080; /* Navy */
}
#expenses {
    top: 480px;
    background-color: #000080; /* Navy */
}
#logout {
    top: 547px;
    background-color: #000080; /* Navy */
}


</style>

<div id="mySidenavAdmin" class="sidenav">
    <a href="/" id="home"> Home </a>
    <a href="/admins" id="admin">Admin</a>
    <a href="/pay" id="pay">Record Payment</a>
    <a href="/rewards" id="reward">Rewards</a>
    <a href="/inventory" id="inventoryY">Inventory</a>
    <a href="/expenses" id="expenses">Expenses</a>
    <a href="/logout" id="logout">Logout</a>
</div>