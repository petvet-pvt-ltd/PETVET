window.onload = function () {
  // Bar Chart
  const barCanvas = document.getElementById('barCanvas');
  const barCtx = barCanvas.getContext('2d');
  const data = [4000, 3000, 5000, 4600, 6000, 5500, 7000];
  const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
  const barWidth = 40;
  const maxHeight = Math.max(...data);
  data.forEach((value, i) => {
    const height = (value / maxHeight) * 200;
    barCtx.fillStyle = '#3b82f6';
    barCtx.fillRect(50 + i * 60, 250 - height, barWidth, height);
    barCtx.fillStyle = '#000';
    barCtx.fillText(labels[i], 50 + i * 60, 265);
  });

  // Pie Chart
  const pieCanvas = document.getElementById('pieCanvas');
  const pieCtx = pieCanvas.getContext('2d');
  const pieData = [40, 25, 15, 10, 10];
  const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
  const labelsPie = ['Vet Visits', 'Grooming', 'Boarding', 'Training', 'Other'];
  let total = pieData.reduce((a, b) => a + b, 0);
  let startAngle = 0;

  pieData.forEach((value, i) => {
    const sliceAngle = (value / total) * 2 * Math.PI;
    pieCtx.fillStyle = colors[i];
    pieCtx.beginPath();
    pieCtx.moveTo(150, 125);
    pieCtx.arc(150, 125, 100, startAngle, startAngle + sliceAngle);
    pieCtx.closePath();
    pieCtx.fill();
    startAngle += sliceAngle;
  });
}
