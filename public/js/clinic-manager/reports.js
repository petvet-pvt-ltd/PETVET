// CSV for headline figures
function downloadCSV(){
  const rows = [
    ["Metric","Value (LKR)"],
    ["Clinic Income", window.reportsData?.appointmentsRevenue ?? ""],
    ["Shop Income",   window.reportsData?.shopRevenue ?? ""]
  ];
  const csv = rows.map(r => r.join(",")).join("\n");
  const blob = new Blob([csv], {type:"text/csv"});
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url; a.download = "petvet_reports.csv";
  document.body.appendChild(a); a.click(); a.remove();
  URL.revokeObjectURL(url);
}

// Better print flow: blank the title (to avoid "Reports" in header),
// add a class hook if you ever need, then restore the title after print.
function printReports(){
  const oldTitle = document.title;
  document.title = " "; // blank so header doesn't show the title if headers are enabled
  const afterPrint = () => {
    document.title = oldTitle;
    window.removeEventListener('afterprint', afterPrint);
  };
  window.addEventListener('afterprint', afterPrint);

  // Heads-up: To hide URL/date/footer lines, uncheck "Headers and footers" in the print dialog.
  window.print();
}
