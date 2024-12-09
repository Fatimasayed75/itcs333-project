function createRoomEquipmentsTable(equipments) {
  const equipmentTableContainer = document.getElementById(
    "equipmentTableContainer"
  );

  if (equipmentTableContainer) {
    // Create table element
    const table = document.createElement("table");
    table.classList.add("table", "table-striped", "table-bordered");

    // Create table header
    const thead = document.createElement("thead");
    const headerRow = document.createElement("tr");

    const headers = ["Equipment Name", "Quantity"];
    headers.forEach((headerText) => {
      const th = document.createElement("th");
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

        // Equipment Name
        const equipmentNameCell = document.createElement("td");
        equipmentNameCell.textContent = equipment["equipName"];
        row.appendChild(equipmentNameCell);

        // Quantity
        const quantityCell = document.createElement("td");
        quantityCell.textContent = equipment["Quantity"];
        row.appendChild(quantityCell);

        tbody.appendChild(row);
      });
    } else {
      const noDataRow = document.createElement("tr");
      const noDataCell = document.createElement("td");
      noDataCell.colSpan = 3;
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
