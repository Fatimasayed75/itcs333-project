function createRoomEquipmentsTable(equipments) {
  const equipmentTableContainer = document.getElementById(
    "equipmentTableContainer"
  );

  if (equipmentTableContainer) {
    // Check if the body has the 'dark-mode' class
    // Create table element with Tailwind CSS classes
    const table = document.createElement("table");
    table.classList.add(
      "min-w-full",
      "border-collapse",
      "shadow-md",
      "rounded-lg",
      "overflow-hidden",
      "my-4"
    );

    // Create table header
    const thead = document.createElement("thead");
    const headerRow = document.createElement("tr");
    headerRow.classList.add(
      "text-left",
    );

    const headers = ["Equipment Name", "Quantity"];
    headers.forEach((headerText) => {
      const th = document.createElement("th");
      th.classList.add(
        "px-6",
        "py-3",
        "border-b",
        "font-medium",
        "uppercase",
        "text-sm"
      );
      th.textContent = headerText;
      headerRow.appendChild(th);
    });
    thead.appendChild(headerRow);
    table.appendChild(thead);

    // Create table body
    const tbody = document.createElement("tbody");

    if (equipments && equipments.length > 0) {
      equipments.forEach((equipment) => {
        const row = document.createElement("tr");
        row.classList.add("hover:bg-gray-500");

        // Equipment Name
        const equipmentNameCell = document.createElement("td");
        equipmentNameCell.classList.add(
          "px-6",
          "py-4",
          "border-b",
          "text-sm",
        );
        equipmentNameCell.textContent = equipment["equipName"];
        row.appendChild(equipmentNameCell);

        // Quantity
        const quantityCell = document.createElement("td");
        quantityCell.classList.add(
          "px-6",
          "py-4",
          "border-b",
          "text-sm",
        );
        quantityCell.textContent = equipment["Quantity"];
        row.appendChild(quantityCell);

        tbody.appendChild(row);
      });
    } else {
      const noDataRow = document.createElement("tr");
      const noDataCell = document.createElement("td");
      noDataCell.colSpan = 2; // Adjust to span across both columns
      noDataCell.classList.add(
        "px-6",
        "py-4",
        "text-center",
        "text-sm",
        "italic"
      );
      noDataCell.textContent = "No equipment available";
      noDataRow.appendChild(noDataCell);
      tbody.appendChild(noDataRow);
    }

    table.appendChild(tbody);

    // Append the table to the container
    equipmentTableContainer.innerHTML = ""; // Clear any existing content
    equipmentTableContainer.appendChild(table);
  } else {
    console.error("Equipment table container not found!");
  }
}
