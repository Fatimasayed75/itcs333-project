<h4 class="text-xl font-semibold text-gray-800 mb-4">Filter Options</h4>
<p class="text-sm text-gray-600 mb-6">Select your filter criteria:</p>

<!-- Filter options -->
<div class="space-y-4">
  <!-- Available Filter -->
  <div>
    <label for="available" class="block text-sm font-medium text-gray-700">Select Availability</label>
    <select id="available"
      class="block w-full mt-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <option value="any">Any</option>
      <option value="yes">Yes</option>
      <option value="no">No</option>
    </select>
  </div>

  <!-- Type Filter -->
  <div>
    <label for="type" class="block text-sm font-medium text-gray-700">Select Type</label>
    <select id="type"
      class="block w-full mt-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <option value="any">Any</option>
      <option value="class">Class</option>
      <option value="lab">Lab</option>
    </select>
  </div>

  <!-- Capacity Filter -->
  <div>
    <label for="capacity" class="block text-sm font-medium text-gray-700">Select Capacity</label>
    <select id="capacity"
      class="block w-full mt-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <option value="any">Any</option>
      <option value="20">20</option>
      <option value="30">30</option>
      <option value="40">40</option>
      <option value="50">50+</option>
    </select>
  </div>
</div>

<!-- Filter Buttons -->
<div class="flex justify-center gap-6 mt-6">
  <button type="button" class="bg-gray-300 text-gray-700 py-2 px-6 rounded-lg hover:bg-gray-400"
    onclick="hideFilterBox()">
    Close
  </button>
  <button type="button" class="text-white py-2 px-6 rounded-lg bg-[#D885A3] hover:bg-[#B66C8E]"
    onclick="applyFilters()">
    Apply Filters
  </button>

  <!-- <button type="button" class="bg-blue-500 text-white py-2 px-6 rounded-lg hover:bg-blue-600" onclick="clearFilters()">
    Clear Filters
  </button> -->
</div>