
const express = require('express');
const cors = require('cors');
const app = express();
const port = 3001; 

//dummy in-memory data (simulating MySQL data)
let studentsData = [
    { id: 'S20240001', name: 'Alice Smith', email: 'alice@uni.edu', course: 'Computer Science', status: 'Active' },
    { id: 'S20240002', name: 'Bob Johnson', email: 'bob@uni.edu', course: 'Mechanical Engineering', status: 'Active' },
    { id: 'S20240003', name: 'Charlie Brown', email: 'charlie@uni.edu', course: 'Physics', status: 'Pending' },
];

app.use(cors()); //enabling CORS for React frontend
app.use(express.json()); //to parse JSON 

// Mock API Endpoint 1: Get All Students
app.get('/api/students', (req, res) => {
    console.log('API: Fetching all students');
    res.json(studentsData);
});

//a mock ock API Endpoint 2: Delete Student Record
app.delete('/api/students/:id', (req, res) => {
    const studentId = req.params.id;
    const initialLength = studentsData.length;
    
    //Simulating the deletion of student data
    studentsData = studentsData.filter(s => s.id !== studentId);

    if (studentsData.length < initialLength) {
        console.log(`API: Deleted student ${studentId}`);
        res.status(200).json({ message: `Student ${studentId} deleted.` });
    } else {
        console.log(`API: Failed to find student ${studentId} for deletion.`);
        res.status(404).json({ message: 'Student not found.' });
    }
});

app.listen(port, () => {
    console.log(`Mock API server running at http://localhost:${port}`);
    console.log("To use the React component, start your React development server and fetch data from this port.");
});