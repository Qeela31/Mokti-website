document.addEventListener("DOMContentLoaded", () => {
    const membersTable = document.getElementById("membersTable").querySelector("tbody");
    const productsTable = document.getElementById("productsTable").querySelector("tbody");
  
    // Dummy data for members
    const members = [
      { id: 1, name: "Qeela", email: "qeela01@example.com" },
      { id: 2, name: "Mina", email: "mina02@example.com" },
    ];
  
    // Populate members table
    members.forEach(member => {
      const row = document.createElement("tr");
      row.innerHTML = `<td>${member.id}</td><td>${member.name}</td><td>${member.email}</td>`;
      membersTable.appendChild(row);
    });
  
    // Add product functionality
    const productForm = document.getElementById("productForm");
    productForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const name = document.getElementById("productName").value;
      const price = document.getElementById("productPrice").value;
  
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${productsTable.rows.length + 1}</td>
        <td>${name}</td>
        <td>${price}</td>
        <td><button>Delete</button></td>
      `;
      productsTable.appendChild(row);
  
      productForm.reset();
    });
  
    // Logout button
    document.getElementById("logoutBtn").addEventListener("click", () => {
      alert("Logged out successfully!");
      window.location.href = "login.html"; // Redirect to login page
    });
  });
  