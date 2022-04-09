<style media="screen">
/* Navbar container */
.navbar {
    overflow: hidden;
    background-color: #333;
    font-family: Arial;
}

/* Links inside the navbar */
.navbar a {
    float: left;
    font-size: 16px;
    color: white;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
}

/* The dropdown container */
.dropdown {
    float: left;
    overflow: hidden;
}

/* Dropdown button */
.dropdown .dropbtn {
    font-size: 16px;
    border: none;
    outline: none;
    color: white;
    padding: 14px 16px;
    background-color: inherit;
    font-family: inherit; /* Important for vertical align on mobile phones */
    margin: 0; /* Important for vertical align on mobile phones */
}

/* Add a red background color to navbar links on hover */
.navbar a:hover, .dropdown:hover .dropbtn {
    background-color: red;
}

/* Dropdown content (hidden by default) */
.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

/* Links inside the dropdown */
.dropdown-content a {
    float: none;
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    text-align: left;
}

/* Add a grey background color to dropdown links on hover */
.dropdown-content a:hover {
    background-color: #ddd;
}

/* Show the dropdown menu on hover */
.dropdown:hover .dropdown-content {
    display: block;
}
</style>
<div class="navbar">
    <a href="index.php">首 頁</a>
    <div class="dropdown">
        <button class="dropbtn">住 宅 管 理
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-content">
            <a href="building_management.php">樓宇管理</a>
            <a href="property_management.php">單位管理</a>
        </div>
    </div>
    <a href="resident_management.php">住 戶 管 理</a>
    <a href="staff_management.php">員 工 管 理</a>
    <div class="dropdown">
        <button class="dropbtn">信 息 管 理
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-content">
            <a href="broadcast_message.php">發送全體電郵</a>
        </div>
    </div>
    <a href="logout.php">登 出</a>
</div>
