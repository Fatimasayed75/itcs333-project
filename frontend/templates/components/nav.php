<?php
require_once '../../../backend/utils/helpers.php';
require_once '../../../backend/utils/constants.php';

use Utils\Constants;
$id = isAuthorized();
// put id in hidden input field
echo "<input type='hidden' id='user-id' value='$id'>";
?>

<!-- SIDEBAR -->
<nav class="sidebar close">
  <header>
    <div class="open-logo-text">
      <div class="image-text">
        <span class="image">
          <img src="../../images/demo.png" alt="UOB Logo" />
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
      <!-- HOME TAB -->
      <li class="nav-link active">
        <a id="home-tab">
          <i class="bx bx-building icon"></i>
          <span class="text nav-text">Browse Rooms</span>
        </a>
      </li>

      <!-- LOG IN TAB FOR GUESTS -->
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

      <!-- DASHBOARD TAB FOR ADMINS -->
      <?php if ($id == Constants::ADMIN_USER_ID) { ?>
        <ul class="menu-links">
          <li class="nav-link">
            <a id="dashboard-tab">
              <i class="bx bx-home-alt icon"></i>
              <span class="text nav-text">Dashboard</span>
            </a>
          </li>
        <?php } ?>

        <!-- BOOKINGS TAB -->
        <?php
        if ($id != Constants::GUEST_USER_ID) {
          echo "<li class='nav-link'>
          <a id='bookings-tab'>
            <i class='bx bx-calendar icon'></i>
            <span class='text nav-text'>Bookings</span>
          </a>";
        }
        ?>

        <!-- PROFILE TAB -->
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

        <!-- NOTIFICATIONS TAB -->
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
        <a class="toggle-dark-mode" role="switch" aria-checked="false" tabindex="0">
          <i class='bx bx-sun icon sun'></i>
          <i class='bx bx-moon icon moon'></i>
          <!-- <div class="toggle-ball"></div> -->
        </a>
      </li>
    </div>
  </div>
</nav>

<!-- Top navigation bar for smaller screens -->

<!-- For Guests -->
<?php if ($id == Constants::GUEST_USER_ID): ?>
  <nav class="top-nav">
    <ul>
      <div class="flex justify-start gap-2">
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
      </div>
      <li class="nav-link">
        <a class="toggle-dark-mode" role="switch" aria-checked="false" tabindex="0">
          <i class='bx bx-sun icon sun'></i>
          <i class='bx bx-moon icon moon'></i>
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
      <?php if ($id != Constants::ADMIN_USER_ID) { ?>
        <li class="nav-link"><a id="bookings-tab"><i class="bx bx-calendar"></i></a></li>
      <?php } ?>
      <li class="nav-link"><a id="profile-tab"><i class="bx bx-user-circle"></i></a></li>
      <li class="nav-link"><a id="notifiations-tab"><i class="bx bx-bell"></i></a></li>
      <li class="nav-link">
        <a class="toggle-dark-mode" role="switch" aria-checked="false" tabindex="0">
          <i class='bx bx-sun icon sun'></i>
          <i class='bx bx-moon icon moon'></i>
        </a>
      </li>
      <li class="nav-link"><a href="../../../backend/server/logout.php"><i class="bx bx-log-out"></i></a></li>
    </ul>
  </nav>
<?php endif; ?>

<?php
$current_page = basename($_SERVER['PHP_SELF']);
if ($id == Constants::GUEST_USER_ID && $current_page !== 'base.php'): ?>
  <div id="main-content" class="content lg:ml-24 md:ml-12">
    <div class="flex flex-col items-center gap-4">
      <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
      <h2 class="text-xl font-semibold">Content Not Available</h2>
      <p class="text-gray-500">Please log in to access the content.</p>
      <a href="/login" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
        Log In
      </a>
    </div>
  </div>
<?php endif; ?>