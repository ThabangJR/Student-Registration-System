
import React, { useState, useEffect } from 'react';
import './StudentDashboard.css'; // Assume a simple CSS file for styling


const useStudents = () => {
    const [students, setStudents] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    const fetchStudents = async () => {
        setLoading(true);
        setError(null);
        try {
            // Fetch data from the Node.js mock API (http://localhost:3001)
            const response = await fetch('http://localhost:3001/api/students');
            if (!response.ok) {
                throw new Error('Failed to fetch data from mock API.');
            }
            const data = await response.json();
            setStudents(data);
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    //useEffect simulates the componentDidMount and componentDidUpdate lifecycle hooks
    useEffect(() => {
        fetchStudents();
    }, []); // Empty dependency array means this runs once after the initial render (like componentDidMount)

    return { students, loading, error, fetchStudents, setStudents };
};


const StudentDashboard = () => {
    //state management using React state (useState)
    const { students, loading, error, fetchStudents, setStudents } = useStudents();
    const [searchTerm, setSearchTerm] = useState('');

    //event Handling: Handle click for deletion
    const handleDelete = async (studentId, studentName) => {
        if (!window.confirm(`Are you sure you want to delete ${studentName}?`)) {
            return;
        }

        try {
            //simulate API call for deletion
            const response = await fetch(`http://localhost:3001/api/students/${studentId}`, {
                method: 'DELETE',
            });

            if (!response.ok) {
                throw new Error('Failed to delete student on the server.');
            }

            //update local state immediately to reflect changes (Real-time update)
            setStudents(prevStudents => prevStudents.filter(s => s.id !== studentId));
            alert(`Successfully deleted ${studentName}.`);

        } catch (err) {
            alert(`Error deleting student: ${err.message}`);
        }
    };

    //filtering logic based on state
    const filteredStudents = students.filter(student => 
        student.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        student.id.toLowerCase().includes(searchTerm.toLowerCase())
    );

    if (loading) return <div className="loading-message">Loading students...</div>;
    if (error) return <div className="error-message">Error: {error}</div>;

    return (
        <div className="react-container">
            <h1>React Student Dashboard</h1>
            <p>Data fetched from Node.js Mock API (Port 3001)</p>

            <input
                type="text"
                placeholder="Filter students by Name or ID"
                value={searchTerm}
                //event Handling
                onChange={(e) => setSearchTerm(e.target.value)}
                className="filter-input"
            />
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Course</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {/*use lists in React to display student records*/}
                    {filteredStudents.map(student => (
                        <tr key={student.id}>
                            <td>{student.id}</td>
                            <td>{student.name}</td>
                            <td>{student.course}</td>
                            <td><span className={`status-${student.status.toLowerCase()}`}>{student.status}</span></td>
                            <td className="react-actions">
                                <button className="btn-view" onClick={() => alert(`Viewing profile for ${student.name}`)}>View</button>
                                {/*Event Handling: Delete button*/}
                                <button className="btn-delete" onClick={() => handleDelete(student.id, student.name)}>Delete</button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
            
            {filteredStudents.length === 0 && <p className="no-results">No students match your filter.</p>}
        </div>
    );
};

export default StudentDashboard;