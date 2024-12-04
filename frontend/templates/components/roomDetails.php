<div class="p-6 m-6 bg-white shadow-lg rounded-lg pb-6 mt-20 sm:mt-15 lg:mt-5 md:mt-10">
  <div class="flex flex-wrap justify-between"> <!-- Flex container for two sections -->

    <!-- Room Details Section -->
    <div class="w-full lg:w-1/2 pr-6 mb-6">
      <h2 class="text-2xl font-bold text-gray-800 my-4 flex items-center">
        <button class="mr-2 text-gray-800 hover:text-gray-800 transition duration-300" onclick="navigateToHomePage();">
          <i class="fa fa-arrow-left"></i>
        </button>
        {{roomID}}
      </h2>
      <p class="text-lg text-gray-800 my-2"><strong>Type:</strong> {{type}}</p>
      <p class="text-lg text-gray-800 my-2"><strong>Capacity:</strong> {{capacity}}</p>
      <p class="text-lg text-gray-800 my-2"><strong>Floor:</strong> {{floor}}</p>
      <p class="text-lg text-gray-800 my-2"><strong>department:</strong> {{department}}</p>
      <p class="text-lg text-gray-800 my-2"><strong>status:</strong> {{isAvailable}}</p>
      <img src="{{image}}" alt="{{roomID}}" class="mt-4 rounded-lg shadow-md h-48">
    </div>

    <!-- Booking Form Section -->
    <div class="w-full lg:w-1/2 pr-6 mb-6">
      <!-- Room Availability -->

      <div>
        <h3 class="text-xl font-semibold text-gray-800 mb-4 text-center">Room Availability</h3>
        <canvas id="roomAvailability" width="400" height="400"></canvas>

      </div>
      <div class="">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 text-center">Book This Room</h3>
        <form id="bookingForm" class="flex flex-col items-center" action="roomDetails.php" method="POST">
          <label for="date" class="block text-gray-700">Select Date:</label>
          <input type="date" id="date" name="date" class="w-full p-2 border rounded mb-4" required />
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
          <button type="submit" class="bg-[#D885A3] text-white py-2 px-4 rounded hover:bg-[#D885A3]">Confirm
            Booking</button>
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
  </div>
</div>
</div>