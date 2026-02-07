import ProductCard from "../components/ProductCard";
import Sidebar from '../components/Sidebar';

const products = [
    {
        name: "MERCEDES - AMG PETRONAS FORMULA ONE TEAM LAS VEGAS LONGSLEEVE TEE MEN",
        oldPrice: 5000,
        price: 3999,
        discount: 20,
        rating: 5,
        image: "/src/assets/images/product1.avif"
    },
    {
        name: "SUMMER SLIDE TRIM GRAPHIC T-SHIRT",
        oldPrice: 1300,
        price: 1100,
        discount: 15,
        rating: 5,
        image: "/src/assets/images/product2.avif"
    },
    {
        name: "VRCT Short Sleeve Tee",
        oldPrice: 200,
        price: 1699,
        discount: 15,
        rating: 5,
        image: "/src/assets/images/product3.avif"
    },
    {
        name: "ADICOLOR 3-STRIPES OVERSIZED TEE",
        oldPrice: 2700,
        price: 2400,
        discount: 11,
        rating: 5,
        image: "/src/assets/images/product4.avif"
    },
    {
        name: "SUMMER SLIDE TRIM GRAPHIC T-SHIRT",
        oldPrice: 2700,
        price: 2400,
        discount: 11,
        rating: 5,
        image: "/src/assets/images/product5.avif"
    },
    {
        name: "Bringback Remixed Argentina Jersey",
        oldPrice: 10000,
        price: 9500,
        discount: 10,
        rating: 5,
        image: "/src/assets/images/product6.avif"
    },
    {
        name: "Common Goal Tee",
        oldPrice: 2300,
        price: 2070,
        discount: 10,
        rating: 5,
        image: "/src/assets/images/product7.avif"
    },
    {
        name: "ULTIMATE365 TEXTURED WOVEN FULL-ZIP Jacket",
        oldPrice: 4800,
        price: 2400,
        discount: 50,
        rating: 5,
        image: "/src/assets/images/product8.avif"
    },
    {
        name: "Own the Run Jacket",
        oldPrice: 400,
        price: 3200,
        discount: 20,
        rating: 5,
        image: "/src/assets/images/product9.avif"
    },
    {
        name: "BEYOND TWISTWEAVE FULL-ZIP WINDBREAKER",
        oldPrice: 7000,
        price: 4500,
        discount: 40,
        rating: 5,
        image: "/src/assets/images/product10.avif"
    },
    {
        name: "A FOOTBALL SHIRT FOR FRIENDLY MATCHES AND CROSS TRAINING",
        oldPrice: 1600,
        price: 1120,
        discount: 30,
        rating: 5,
        image: "/src/assets/images/product11.avif"
    },
    {
        name: "adidas Crazy Lite Jersey",
        oldPrice: 2520,
        price: 2800,
        discount: 10,
        rating: 5,
        image: "/src/assets/images/product12.avif"
    },
];

const ProductList = () => {
    return (
        <div className="container">
            <div className="row">

                {/* Sidebar */}
                <div className="col-lg-2 col-md-3 mb-4">
                    <Sidebar />
                </div>

                {/* Products */}
                <div className="col-lg-10 col-md-9">
                    <h2 className="mb-3">All Products</h2>

                    <div className="row">
                        {products.map((product, index) => (
                            <div className="col-lg-3 col-md-4 col-sm-6 mb-4" key={index}>
                                <ProductCard product={product} />
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>

    );
};

export default ProductList;