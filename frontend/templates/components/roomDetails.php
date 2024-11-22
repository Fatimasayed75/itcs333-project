<div class="p-6 m-6 bg-white shadow-lg rounded-lg text-left pb-6 mt-20 sm:mt-15 lg:mt-5 md:mt-10">
  <!-- Room Details -->
  <h2 class="text-2xl font-bold text-gray-800 mt-4">
    <button id="backToHomeBtn">
      <i class="fa fa-arrow-left mr-2"></i> <!-- Icon to the left of room name -->
    </button>
    {{roomID}}
  </h2>
  <p class="text-lg text-gray-600">Type: {{type}}</p>
  <p class="text-lg text-gray-600">Capacity: {{capacity}}</p>
  <img src="{{image}}" alt="{{roomID}}" class="mt-4">
</div>