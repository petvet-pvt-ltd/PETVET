// Simple placeholders for finance charts
window.addEventListener('load', function(){
  const lineChart = document.getElementById('lineChart');
  if (lineChart) {
    const ctx = lineChart.getContext('2d');
    ctx.fillStyle = '#3b82f6';
    // Draw a simple line-like bars as placeholder
    for (let i=0;i<10;i++) {
      const h = 20 + Math.random()*150;
      ctx.fillRect(20 + i*35, 220 - h, 16, h);
    }
  }
  const pieChart = document.getElementById('pieChart');
  if (pieChart) {
    const ctx2 = pieChart.getContext('2d');
    const data = [40, 25, 20, 10, 5];
    const colors = ['#2563eb','#10b981','#f59e0b','#ef4444','#8b5cf6'];
    const total = data.reduce((a,b)=>a+b,0);
    let start = 0;
    data.forEach((v,i)=>{
      const angle = (v/total)*Math.PI*2;
      ctx2.fillStyle = colors[i];
      ctx2.beginPath();
      ctx2.moveTo(150,125);
      ctx2.arc(150,125,100,start,start+angle);
      ctx2.closePath();
      ctx2.fill();
      start += angle;
    });
  }
});
