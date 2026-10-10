import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { useAuth } from './context/AuthContext';
import Login from './pages/Login';
import Register from './pages/Register';
import Home from './pages/Home.tsx';
import Profile from './pages/Profile';
import BaseLayout from './components/BaseLayout.tsx'
import GearDetail from './pages/GearDetail.tsx';
import RentalDetail from './pages/RentalDetail.tsx';
import ConfirmHandover from './pages/ConfirmHandover.tsx';
import MyRentals from './pages/MyRentals.tsx';

function ProtectedRoute() {
  const { user, loading } = useAuth();

  if (loading) return <p>Betöltés...</p>;
  if (!user) return <Navigate to="/login" replace />;

  return <BaseLayout />;
}

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />

        <Route element={<ProtectedRoute />}>
          <Route path="/" element={<Home />} />
          <Route path="/profile" element={<Profile />} />
          <Route path="/gears/:id" element={<GearDetail />} />
          <Route path="/rentals" element={<MyRentals />} />
          <Route path="/rentals/:id" element={<RentalDetail />} />
          <Route path="/rentals/:id/confirm-handover" element={<ConfirmHandover />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}