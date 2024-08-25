import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { Navbar, Nav } from 'react-bootstrap';

const Navigation = () => {
  const location = useLocation();

  const getTitle = () => {
    switch (location.pathname) {
      case '/':
        return 'Home';
      case '/add-product':
        return 'Add Product';
      default:
        return 'My Application';
    }
  };

  return (
    <Navbar bg="dark" data-bs-theme="dark" sticky="top" expand="lg" className="shadow-sm">
      <div className="container">

        <Navbar.Brand as={Link} to="/">
          {getTitle()}
        </Navbar.Brand>
        <Nav className="ml-auto">
          <Nav.Link as={Link} to="/">Home</Nav.Link>
          <Nav.Link as={Link} to="/add-product">Add</Nav.Link>
        </Nav>
      </div>

    </Navbar>

  );
};

export default Navigation;
