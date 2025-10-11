// Line Chart
const lineCtx = document.getElementById("lineChart").getContext("2d");
const lineData = [12500,10800,14200,15800,18500,20200,24500];
lineCtx.beginPath();
lineCtx.moveTo(20,250-(lineData[0]/150));
lineData.forEach((v,i)=>{
  lineCtx.lineTo(20+i*50,250-(v/150));
});
lineCtx.strokeStyle="#2563eb";
lineCtx.stroke();

// Pie Chart
const pieCtx = document.getElementById("pieChart").getContext("2d");
const pieData = [45,25,15,10,5];
const colors = ["#2563eb","#16a34a","#f59e0b","#9333ea","#6b7280"];
let total=pieData.reduce((a,b)=>a+b,0), start=0;
pieData.forEach((val,i)=>{
  let slice = (val/total)*2*Math.PI;
  pieCtx.beginPath();
  pieCtx.moveTo(150,125);
  pieCtx.arc(150,125,100,start,start+slice);
  pieCtx.closePath();
  pieCtx.fillStyle=colors[i];
  pieCtx.fill();
  start+=slice;
});
