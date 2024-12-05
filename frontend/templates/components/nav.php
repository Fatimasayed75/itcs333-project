<?php
require_once '../../../backend/utils/helpers.php';
require_once '../../../backend/utils/constants.php';

use Utils\Constants;
$id = isAuthorized();
var_dump($id);
?>

<nav class="sidebar close">
  <header>
    <div class="open-logo-text">
      <div class="image-text">
        <span class="image">
          <img src="../../images/demo.png" alt="" />
        </span>

        <div class="text logo-text">
          <span class="name">IT College</span>
          <span class="system">Room Booking</span>
        </div>
      </div>
    </div>

    <i class="bx bx-menu toggle"></i>
  </header>

  <div class="menu-bar">
    <div class="menu">
      <li class="nav-link active">
        <a id="home-tab">
          <i class="bx bx-building icon"></i>
          <span class="text nav-text">Browse Rooms</span>
        </a>
      </li>

      <?php
      if ($id == Constants::GUEST_USER_ID) {
        echo '<li class="nav-link">
                <a href="../../../backend/server/logout.php">
                  <i class="bx bxs-party icon"></i>
                  <span class="text nav-text">Log in for more!</span>
                </a>
              </li>';
        }
      ?>


      <?php if ($id == Constants::ADMIN_USER_ID) { ?>
        <ul class="menu-links">
          <li class="nav-link">
            <a id="dashboard-tab">
              <i class="bx bx-home-alt icon"></i>
              <span class="text nav-text">Dashboard</span>
            </a>
          </li>
        <?php } ?>

        <?php
        if ($id != Constants::GUEST_USER_ID) {
          echo "<li class='nav-link'>
          <a id='bookings-tab'>
            <i class='bx bx-calendar icon'></i>
            <span class='text nav-text'>Bookings</span>
          </a>";
        }
        ?>

        <?php
        if ($id != Constants::GUEST_USER_ID) {
          echo "<li class='nav-link'>
          <a id='profile-tab' onclick='loadContent('profile.php')'>
            <i class='bx bx-user-circle icon'></i>
            <span class='text nav-text'>Profile</span>
          </a>
        </li>";
        }
        ?>

        <?php
        if ($id != Constants::GUEST_USER_ID) {
          echo "<li class='nav-link'>
          <a id='notifiations-tab'>
            <i class='bx bx-bell icon'></i>
            <span class='text nav-text'>Notifications</span>
          </a>
        </li>";
        }
        ?>
        </li>
      </ul>
    </div>

    <div class="bottom-content">
      <?php
      if ($id != Constants::GUEST_USER_ID) {
        echo "<li class='nav-link'>
        <a href='../../../backend/server/logout.php'>
          <i class='bx bx-log-out icon'></i>
          <span class='text nav-text'>Logout</span>
        </a>";
      }
      ?>

      <li class="mode">
        <div class="sun-moon">
          <i class="bx bx-moon icon moon"></i>
        </div>
        <span class="mode-text text">Dark mode</span>

        <div class="toggle-switch">
          <span class="switch"></span>
        </div>
      </li>
    </div>
  </div>
</nav>

<!-- Top navigation bar for smaller screens -->

<!-- For Guests -->
<?php if ($id == Constants::GUEST_USER_ID): ?>
  <nav class="top-nav">
    <ul>
      <li class="nav-link active">
        <a id="home-tab">
          <i class="bx bx-building mr-2"></i>
          <span class="text text-xs">Browse Rooms</span>
        </a>
      </li>

      <li class="nav-link">
        <a href="../../../backend/server/logout.php">
          <i class="bx bxs-party icon mr-2"></i>
          <span class="text text-xs">Log in for more!</span>
        </a>
      </li>
      <li class="nav-link">
        <a id="" class="toggle-dark-mode">
          <i class="bx bx-moon icon moon mr-2"></i>
          <span class="text text-xs">Dark mode</span>
        </a>
      </li>

    </ul>
  </nav>
<?php endif; ?>

<!-- For users -->
<?php if ($id != Constants::GUEST_USER_ID): ?>
  <nav class="top-nav">
    <ul>
      <li class="nav-link active"><a id="home-tab"><i class="bx bx-building"></i></a></li>
      <?php if ($id === Constants::ADMIN_USER_ID) { ?>
        <li class="nav-link"><a id="dashboard-tab"><i class="bx bx-home-alt"></i></a></li>
      <?php } ?>
      <li class="nav-link"><a id="bookings-tab"><i class="bx bx-calendar"></i></a></li>
      <li class="nav-link"><a id="profile-tab"><i class="bx bx-user-circle"></i></a></li>
      <li class="nav-link"><a id="notifiations-tab"><i class="bx bx-bell"></i></a></li>
      <li class="nav-link"><a id="" class="toggle-dark-mode"><i class="bx bx-moon"></i></a></li>
      <li class="nav-link"><a href="../../../backend/server/logout.php"><i class="bx bx-log-out"></i></a></li>
    </ul>
  </nav>
<?php endif; ?>