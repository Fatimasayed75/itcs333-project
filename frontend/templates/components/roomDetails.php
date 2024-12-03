<div class="p-6 m-6 bg-white shadow-lg rounded-lg text-left pb-6 mt-20 sm:mt-15 lg:mt-5 md:mt-10">
  <!-- Room Details -->
  <h2 class="text-2xl font-bold text-gray-800 mt-4 flex items-center">
    <button id="backToHomeBtn" class="mr-2">
      <i class="fa fa-arrow-left"></i> <!-- Icon to the left of room name -->
    </button>
    {{roomID}}
  </h2>
  <p class="text-lg text-gray-600">Type: {{type}}</p>
  <p class="text-lg text-gray-600">Capacity: {{capacity}}</p>
  <p><strong>Floor:</strong> {{floor}}</p>
  <p><strong>Status:</strong> {{isAvailable}}</p>
  <button id="bookRoomBtn" class="bg-blue-500 text-white py-2 px-4 rounded mt-4 hover:bg-blue-600">Book Room</button>
  <img src="{{image}}" alt="{{roomID}}" class="mt-4 rounded-lg shadow-md">
</div>