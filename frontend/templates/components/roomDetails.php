<div class="p-6 m-6 bg-white shadow-lg rounded-lg pb-6 mt-20 sm:mt-15 lg:mt-5 md:mt-10">
  <div class="flex flex-wrap justify-around"> <!-- Flex container for two sections -->

    <!-- Room Details Section -->
    <div class="w-full lg:w-1/3 mb-6 mx-auto flex flex-col">
      <h2 class="text-2xl font-bold text-gray-800 my-4 flex items-center justify-center">
        <button class="mr-2 text-gray-800 hover:text-gray-800 transition duration-300" onclick="navigateToHomePage();">
          <i class="fa fa-arrow-left"></i>
        </button>
        {{roomID}}
      </h2>
      <p class="text-lg text-gray-800 my-2 rd-p"><strong>Type:</strong> {{type}}</p>
      <p class="text-lg text-gray-800 my-2 rd-p"><strong>Capacity:</strong> {{capacity}}</p>
      <p class="text-lg text-gray-800 my-2 rd-p"><strong>Floor:</strong> {{floor}}</p>
      <p class="text-lg text-gray-800 my-2 rd-p"><strong>Department:</strong> {{department}}</p>
      <p class="text-lg text-gray-800 my-2 rd-p"><strong>Status:</strong> {{isAvailable}}</p>
      <img src="{{image}}" alt="{{roomID}}" class="mt-4 h-58">
      <div id="equipmentTableContainer"></div>
    </div>

    <!-- Booking Form Section -->
    <div class="w-full lg:w-1/2 mb-6 bookingSection">
      <!-- Room Availability -->
      <div class="flex justify-center mb-4">
        <h3 class="text-xl font-semibold text-gray-800" id="roomAvailability-h3">Room Availability</h3>
        <button id="refreshBtn" class="ml-4">
          <i class="fa fa-refresh"></i>
        </button>
      </div>

      <div class="flex justify-center">
        <button id="prevWeek" class="mx-16">
          <i class="fa fa-arrow-left"></i>
        </button>
        <p id="weekOffset">Current Week</p>
        <button id="nextWeek" class="mx-16">
          <i class="fa fa-arrow-right"></i>
        </button>
      </div>
      <canvas id="roomAvailability" width="400" height="400" class="p-6"></canvas>

      <div id="bookingForm">
        <h3 class="text-xl font-semibold mb-4 text-center">Book This Room</h3>
        <div id="bookingForm" class="flex flex-col items-center" action="roomDetails.php" method="POST">

          <!-- Select Available Date -->
          <label for="date" class="block text-gray-700">Select Date:</label>
          <input type="date" id="date" name="date" class="w-full p-2 border rounded mb-4" required
            onchange="loadAvailableTimes('{{roomID}}')">

          <!-- Select Available Start Time -->
          <label for="startTime" class="block text-gray-700">Start Time:</label>
          <select id="startTime" name="startTime" class="w-full p-2 border rounded mb-4" required>
            <option value="" disabled selected>Select a date first</option>
          </select>

          <!-- Select Duration -->
          <label for="duration" class="block text-gray-700">Duration (in minutes):</label>
          <select id="duration" name="duration" class="w-full p-2 border rounded mb-4" required>
            <option value="30">30 minutes</option>
            <option value="60">60 minutes</option>
            <option value="90">90 minutes</option>
            <option value="120">120 minutes</option>
            <option value="150">150 minutes</option>
          </select>

        <div id="errorMessage" class="hidden text-[#D885A3] text-sm mb-4"></div>

          <button type="button" onclick="confirmBooking('{{roomID}}')" class="bg-[#D885A3] text-white py-2 px-4 rounded hover:bg-[#D885A3]">Confirm Booking</button>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
  <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full flex flex-col items-center text-center">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Booking Successful!</h2>
    <p class="text-lg text-gray-600 mb-4">Your booking has been confirmed successfully</p>
    <button id="closeModalBtn" class="bg-[#D885A3] text-white py-2 px-4 rounded hover:bg-[#D885A3]">Close</button>
  </div>
</div>

<!-- Special Success Modal for S40-1002 and S40-2001 -->
<div id="specialSuccessModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
  <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full flex flex-col items-center text-center">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Pending Approval</h2>
    <p class="text-lg text-gray-600 mb-4">Your request has been sent to the admin for approval.</p>
    <button id="closeSpecialModalBtn" class="bg-[#D885A3] text-white py-2 px-4 rounded hover:bg-[#D885A3]">Close</button>
  </div>
</div>