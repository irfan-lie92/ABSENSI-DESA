document.addEventListener('DOMContentLoaded', function() {
    // Initialize signature pads
    document.querySelectorAll('.signature-pad').forEach(canvas => {
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)'
        });
        
        // Set canvas width to match its parent element
        canvas.width = canvas.parentElement.clientWidth;
        canvas.height = 50;
        
        signaturePad.onEnd = async function() {
            const staffId = canvas.dataset.staffId;
            const date = canvas.dataset.date;
            const signature = signaturePad.toDataURL();
            
            try {
                const response = await fetch('api/save_attendance.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        staff_id: staffId,
                        date: date,
                        signature: signature
                    })
                });
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const data = await response.json();
                if (data.success) {
                    // Update the UI to show the saved signature
                    const dateSpan = canvas.nextElementSibling;
                    dateSpan.textContent = new Date(date).getDate();
                } else {
                    alert('Failed to save attendance: ' + data.message);
                    signaturePad.clear();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to save attendance. Please try again.');
                signaturePad.clear();
            }
        };
    });
});

function previousWeek() {
    const urlParams = new URLSearchParams(window.location.search);
    let week = parseInt(urlParams.get('week')) || getCurrentWeek();
    let year = parseInt(urlParams.get('year')) || getCurrentYear();
    
    week--;
    if (week < 1) {
        week = 52;
        year--;
    }
    
    window.location.href = `index.php?week=${week}&year=${year}`;
}

function nextWeek() {
    const urlParams = new URLSearchParams(window.location.search);
    let week = parseInt(urlParams.get('week')) || getCurrentWeek();
    let year = parseInt(urlParams.get('year')) || getCurrentYear();
    
    week++;
    if (week > 52) {
        week = 1;
        year++;
    }
    
    window.location.href = `index.php?week=${week}&year=${year}`;
}

function getCurrentWeek() {
    const now = new Date();
    const start = new Date(now.getFullYear(), 0, 1);
    const diff = now - start;
    const oneWeek = 1000 * 60 * 60 * 24 * 7;
    return Math.ceil(diff / oneWeek);
}

function getCurrentYear() {
    return new Date().getFullYear();
}