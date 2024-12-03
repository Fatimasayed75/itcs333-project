<div class="p-6 m-6 bg-white shadow-lg rounded-lg text-left pb-6 mt-20 sm:mt-15 lg:mt-5 md:mt-10 flex flex-wrap lg:flex-nowrap">
  <!-- Room Details Section -->
  <div class="w-full lg:w-1/3 pr-6">
    <h2 class="text-2xl font-bold text-gray-800 mt-4 flex items-center">
      <button class="mr-2" onclick="window.history.back();">
        <i class="fa fa-arrow-left"></i>
      </button>
      {{roomID}}
    </h2>
    <p class="text-lg text-gray-600">Type: {{type}}</p>
    <p class="text-lg text-gray-600">Capacity: {{capacity}}</p>
    <p><strong>Floor:</strong> {{floor}}</p>
    <p><strong>Status:</strong> {{isAvailable}}</p>
    <img src="{{image}}" alt="{{roomID}}" class="mt-4 rounded-lg shadow-md">
  </div>

  <!-- Availability Graph Section -->
  <div class="w-full lg:w-1/3 px-6 text-center">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Room Availability</h3>
    <div class="availability-graph mx-auto">
      <div class="graph-header">
        <span class="date-label">Date</span>
        <span class="time-label">8:00</span>
        <span class="time-label">10:00</span>
        <span class="time-label">12:00</span>
        <span class="time-label">14:00</span>
        <span class="time-label">16:00</span>
        <span class="time-label">18:00</span>
        <span class="time-label">20:00</span>
      </div>
      <div class="graph-body">
        <div class="date-row">
          <span class="date-label">06/02/2019</span>
          <div class="time-slot booked"></div>
          <div class="time-slot"></div>
          <div class="time-slot booked"></div>
          <div class="time-slot"></div>
          <div class="time-slot booked"></div>
          <div class="time-slot"></div>
          <div class="time-slot"></div>
        </div>
        <div class="date-row">
          <span class="date-label">07/02/2019</span>
          <div class="time-slot"></div>
          <div class="time-slot booked"></div>
          <div class="time-slot"></div>
          <div class="time-slot"></div>
          <div class="time-slot"></div>
          <div class="time-slot booked"></div>
          <div class="time-slot"></div>
        </div>
        <div class="date-row">
          <span class="date-label">08/02/2019</span>
          <div class="time-slot booked"></div>
          <div class="time-slot"></div>
          <div class="time-slot booked"></div>
          <div class="time-slot"></div>
          <div class="time-slot booked"></div>
          <div class="time-slot"></div>
          <div class="time-slot"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Booking Form Section -->
  <div class="w-full lg:w-1/3 pl-6">
    <h3 class="text-xl font-semibold text-gray-800 mb-4 text-center">Book This Room</h3>
    <form id="bookingForm" class="flex flex-col items-center" action="roomDetails.php" method="POST">
      <label for="date" class="block text-gray-700">Select Date:</label>
      <input
        type="date"
        id="date"
        name="date"
        class="w-full p-2 border rounded mb-4"
        required
      />
      <label for="startTime" class="block text-gray-700">Start Time:</label>
      <select id="startTime" name="startTime" class="w-full p-2 border rounded mb-4" required>
        <?php
          $startTime = 8; // Starting hour (8:00 AM)
          $endTime = 20; // Ending hour (8:00 PM)
          while ($startTime <= $endTime) {
            $hours = floor($startTime);
            $minutes = ($startTime - $hours) * 60;
            $timeString = sprintf('%02d:%02d', $hours % 12 ?: 12, $minutes == 0 ? '00' : $minutes);
            $timeString .= $hours >= 12 ? ' PM' : ' AM';
            echo "<option value=\"$timeString\">$timeString</option>";
            $startTime += 0.5; // Increment by 30 minutes
          }
        ?>
      </select>
      <label for="duration" class="block text-gray-700">Duration (in minutes):</label>
      <select id="duration" name="duration" class="w-full p-2 border rounded mb-4" required>
        <option value="30">30 minutes</option>
        <option value="60">60 minutes</option>
        <option value="90">90 minutes</option>
        <option value="120">120 minutes</option>
        <option value="150">150 minutes</option>
      </select>
      <button type="submit" class="bg-[#D885A3] text-white py-2 px-4 rounded hover:bg-[#D885A3]">Confirm Booking</button>
    </form>
    <p id="formMessage" class="mt-4 text-sm text-gray-700">
      <?php
        if (isset($_POST['date'])) {
          echo "Booking successful!";
        }
      ?>
    </p>
  </div>
</div>

<!-- Updated CSS -->
<style>
  .availability-graph {
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: center;
  }

  .graph-header, .date-row {
    display: flex;
    justify-content: space-evenly;
    align-items: center;
  }

  .date-label, .time-label {
    width: 70px;
    text-align: center;
  }

  .date-row {
    align-items: center;
  }

  .time-slot {
    width: 70px;
    height: 25px;
    background-color: #e0e0e0;
    border-radius: 5px;
  }

  .time-slot.booked {
    background-color: #D885A3;
  }

  button {
    position: relative;
    z-index: 10;
  }
</style>
