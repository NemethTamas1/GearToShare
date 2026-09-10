import { createContext, useContext, useState, useEffect } from 'react';
import type { ReactNode } from 'react';
import api, { getCsrfCookie } from '../lib/axios.ts';
import type { User, RegisterPayload, LoginPayload, AuthContextType } from '../types/auth.ts';

const AuthContext = createContext<AuthContextType | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  const fetchUser = async () => {
    try {
      const res = await api.get('/api/me');
      setUser(res.data.user);
    } catch {
      setUser(null);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchUser();
  }, []);

  const register = async (data: RegisterPayload) => {
    await getCsrfCookie();
    const res = await api.post('/api/register', data);
    setUser(res.data.user);
  };

  const login = async (data: LoginPayload) => {
    await getCsrfCookie();
    const res = await api.post('/api/login', data);
    setUser(res.data.user);
  };

  const logout = async () => {
    await api.post('/api/logout');
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, loading, register, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (!context) throw new Error('useAuth csak AuthProvider-en belül használható');
  return context;
}