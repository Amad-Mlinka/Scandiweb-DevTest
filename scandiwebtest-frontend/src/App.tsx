// App.tsx
import React from 'react';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import './assets/scss/styles.scss';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'font-awesome/css/font-awesome.min.css';
import Navigation from './components/navigation';
import IndexPage from './pages';
import AddProductPage from './pages/addPage';
import 'react-toastify/dist/ReactToastify.css';
function App() {
  return (

      <div className="App">
        <Router>
          <Navigation />
          <div className="container">
            <Routes>
                <Route path="/" element={<IndexPage />} />
                <Route path="/add-product" element={<AddProductPage />} />
            </Routes>
          </div>
        </Router>
      </div>
  );
}

export default App;
