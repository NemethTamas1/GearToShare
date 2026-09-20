import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { useAuth } from './context/AuthContext';
import Login from './pages/Login';
import Register from './pages/Register';
import Home from './pages/Home.tsx';
import Profile from './pages/Profile';
import BaseLayout from './components/BaseLayout.tsx'

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
        </Route>
      </Routes>
    </BrowserRouter>
  );
}