import { Link, useLocation } from 'react-router-dom';

const Navbar = () => {
    const location = useLocation();

    return (
        <nav className="navbar navbar-expand-lg bg-body-tertiary border-bottom border-body" data-bs-theme="light">
            <div className="container">
                <button className="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span className="navbar-toggler-icon"></span>
                </button>
                <div className="justify_content_center">

                </div>
                <Link className="navbar-brand" to="/">
                    <img src="/src/assets/images/logo.png" alt="Logo" width="100px" height="100px"
                        className="d-inline-block align-text " />
                </Link>
                <div className="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul className="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li className="nav-item px-2">
                            <Link className={`nav-link ${location.pathname === '/' ? 'active' : ''}`} to="/">Home</Link>
                        </li>
                        <li className="nav-item px-2">
                            <Link className={`nav-link ${location.pathname === '/products' ? 'active' : ''}`} to="/products">Products</Link>
                        </li>
                        <li className="nav-item px-2">
                            <Link className={`nav-link ${location.pathname === '/blog' ? 'active' : ''}`} to="/blog">Blogs</Link>
                        </li>
                        <li className="nav-item px-2">
                            <Link className={`nav-link ${location.pathname === '/contact' ? 'active' : ''}`} to="/contact">Contact</Link>
                        </li>
                        <li className="nav-item px-2">
                            <Link className={`nav-link ${location.pathname === '/policies' ? 'active' : ''}`} to="/policies">Policies</Link>
                        </li>
                        <li className="nav-item px-2">
                            <Link className={`nav-link ${location.pathname === '/about' ? 'active' : ''}`} to="/about">About</Link>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    );

};

export default Navbar;
